package com.example.demo.business;

import org.springframework.security.core.authority.SimpleGrantedAuthority;
import org.springframework.security.core.userdetails.User;
import org.springframework.security.core.userdetails.UserDetails;
import org.springframework.security.core.userdetails.UserDetailsService;
import org.springframework.security.core.userdetails.UsernameNotFoundException;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import com.example.demo.repository.UsuarioRepository;
import com.example.demo.entity.Rol;
import com.example.demo.entity.Usuario;
import java.util.List;

@Service
public class CustomUserDetailsBusiness implements UserDetailsService{

    @Autowired
    private UsuarioRepository usuarioRepository;
    
    @Override
    public UserDetails loadUserByUsername(String mail) throws UsernameNotFoundException {
        Usuario usuario = usuarioRepository.findByMail(mail);
        if (usuario == null) {
            throw new UsernameNotFoundException("El Usuario no se Encuentra Registrado");
        }
        return User.withUsername(usuario.getMail())
                .password(usuario.getPass())
                .authorities(List.of(new SimpleGrantedAuthority("ROLE_" + usuario.getRol().name())))
                .build();
    }

}
