package com.example.demo.controller;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PostMapping;

import com.example.demo.business.CustomUserDetailsBusiness;

@Controller
public class AuthConcursanteController {
    
    @Autowired
    private CustomUserDetailsBusiness customUserDetailsBusiness;

    
    @GetMapping("/")
    public String principal(){
        return "loginConcursante";
    }
    
    @GetMapping("/loginConcursante")
    public String loginConcursante(){
        return "loginConcursante";
    }

    @GetMapping("/inicioConcursante")
    public String inicioConcursante(){
        return "inicioConcursante";
    }

    

}
