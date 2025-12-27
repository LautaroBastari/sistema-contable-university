
package com.example.demo.controller;

import java.io.IOException;
import java.util.List;
import java.util.stream.Collectors;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.core.io.Resource;
import org.springframework.http.HttpHeaders;
import org.springframework.http.ResponseEntity;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.ExceptionHandler;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.ResponseBody;
import org.springframework.web.multipart.MultipartFile;
import org.springframework.web.servlet.mvc.method.annotation.MvcUriComponentsBuilder;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;

import com.example.demo.business.EvaluacionBusiness;
import com.example.demo.business.UsuarioBusiness;
import com.example.demo.entity.Evaluacion;
import com.example.demo.entity.Rol;
import com.example.demo.entity.Usuario;
import com.example.demo.uploadingFiles.storage.StorageFileNotFoundException;
import com.example.demo.uploadingFiles.storage.StorageService;

import org.springframework.security.core.Authentication;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.PostMapping;
import com.example.demo.business.MaterialEducativoBusiness;

import com.example.demo.entity.MaterialEducativo;

@Controller
public class EvaluacionController {

    @Autowired
    EvaluacionBusiness evaluacionBusiness;

    @Autowired
    UsuarioBusiness usuarioBusiness;

    @Autowired
    MaterialEducativoBusiness materialEducativoBusiness;


    @GetMapping("/evaluacionAsignada.html")
    public String panelEvaluacionAsignada(@RequestParam String mailEvaluador, Model model){
        Evaluacion evaluacion = evaluacionBusiness.obtenerEvaluacion(mailEvaluador);
        model.addAttribute("evaluacion", evaluacion);
        MaterialEducativo material = materialEducativoBusiness.obtenerMaterialPorId(evaluacion.getMaterial().getIdMaterial());
        model.addAttribute("material", material);
        return "evaluacionAsignada";
    }

    @PostMapping("/evaluacionAsignada.html")
    public String evaluacionAsignadaPost(Model model) {
    Authentication authentication = SecurityContextHolder.getContext().getAuthentication();
    if (authentication != null && authentication.isAuthenticated()) {
        String mailEvaluador = authentication.getName();
        try {
            Evaluacion evaluacion = evaluacionBusiness.obtenerEvaluacion(mailEvaluador);
            model.addAttribute("evaluacion", evaluacion);
            return "evaluacionAsignada"; 
        } catch (RuntimeException e) {
            model.addAttribute("error", "El usuario evaluador no tiene materiales asignados");
            return "errorPage"; 
        }
    } else {

        return "redirect:/loginConcursante"; 
    }
    }

    @PostMapping("/guardarEvaluacionDescripcion")
    public String guardarEvaluacionController(@RequestParam("descripcionEvaluacion") String descripcionEvaluacion, Model model) {
        String mailEvaluador = SecurityContextHolder.getContext().getAuthentication().getName();
        System.out.println("Correo del evaluador: " + mailEvaluador);
        Evaluacion evaluacion = evaluacionBusiness.obtenerEvaluacion(mailEvaluador);
        if (evaluacion != null && evaluacion.getEvaluacion()==null) {
            evaluacion.setEvaluacion(descripcionEvaluacion);
            evaluacionBusiness.guardarEvaluacion(evaluacion);
            model.addAttribute("exito", "La evaluación se ha guardado exitosamente.");
        } else {
            model.addAttribute("error", "Ya hay una evaluacion asignada.");
        }
        return "inicioConcursante.html"; 
    }
    
}
