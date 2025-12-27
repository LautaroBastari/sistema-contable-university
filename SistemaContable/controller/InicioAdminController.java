package com.example.demo.controller;
import java.util.List;
import java.util.stream.Collectors;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;

import com.example.demo.business.UsuarioBusiness;
import com.example.demo.entity.Rol;
import com.example.demo.entity.Usuario;

@Controller
public class InicioAdminController {

    @Autowired
    UsuarioBusiness usuarioBusiness;
    @GetMapping("/inicioAdmin")
    public String inicioAdmin() {
        return "inicioAdmin";
    }
    
    @GetMapping("/seleccionMateriales")
    public String publicarMateriales(Model model) {
        List<Usuario> usuarios = usuarioBusiness.obtenerTodosLosUsuarios();
        List<Usuario> concursantes = usuarios.stream()
            .filter(usuario -> Rol.CONCURSANTE.equals(usuario.getRol()))
            .collect(Collectors.toList());
        model.addAttribute("usuarios", concursantes);
        return "seleccionMateriales";
    }
}
