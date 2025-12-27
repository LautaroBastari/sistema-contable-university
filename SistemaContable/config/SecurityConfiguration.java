package com.example.demo.config;

import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.context.annotation.Primary;
import org.springframework.http.HttpMethod;
import org.springframework.security.config.annotation.web.builders.HttpSecurity;
import org.springframework.security.config.annotation.web.configuration.EnableWebSecurity;
import org.springframework.security.core.userdetails.UserDetailsService;
import org.springframework.security.web.SecurityFilterChain;
import org.springframework.security.web.util.matcher.AntPathRequestMatcher;

import com.example.demo.uploadingFiles.storage.StorageProperties;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.beans.factory.annotation.Value;

@Configuration
@EnableWebSecurity
public class SecurityConfiguration {

@Value("${storage.location}")
    private String storageLocation;

    @Autowired
    private UserDetailsService userDetailsService;

    @Bean
    public SecurityFilterChain filterChain(HttpSecurity http) throws Exception{
        http.userDetailsService(userDetailsService)
                .authorizeHttpRequests((requests) -> requests
                        .requestMatchers("/css/**", "/img/**", "/fonts/**").permitAll()
                        .requestMatchers("/", "inicioAdmin", "/formularioRegistro", "/listaUsuarios", "/usuarioEliminado", "/usuarioActualizado", "/formularioModificarUsuario").permitAll()
                        .requestMatchers(HttpMethod.POST, "/buscarUsuario/**", "/actualizarUsuario/**", "/eliminarUsuario/**").permitAll()
                        .requestMatchers("/inicioConcursante").hasAnyRole( "EVALUADOR", "CONCURSANTE", "ADMINISTRADOR")
                        .anyRequest().authenticated()
                )
                .formLogin(httpSecurityFormLoginConfigurer -> httpSecurityFormLoginConfigurer
                        .loginPage("/loginConcursante").permitAll()
                        .defaultSuccessUrl("/inicioConcursante")
                )
                .logout (httpSecurityLogoutConfigurer -> httpSecurityLogoutConfigurer.permitAll()
                        .logoutRequestMatcher (new AntPathRequestMatcher("/logout"))
                        .logoutSuccessUrl ("/loginConcursante")
                )

                .csrf().disable(); // Deshabilitar CSRF para permitir solicitudes POST desde formularios

        return http.build();
    }

        @Bean
        @Primary
        public StorageProperties securityStorageProperties() {
                StorageProperties properties = new StorageProperties();
                properties.setLocation(storageLocation);
                return properties;
        }

}