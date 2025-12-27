package com.example.demo.controller;

import java.io.File;
import java.io.IOException;

import org.hibernate.annotations.DialectOverride.GeneratedColumn;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.multipart.MultipartFile;
import org.springframework.security.core.Authentication;  //Agregado 26-3
import org.springframework.ui.Model; //Agregado 4-4

import com.example.demo.business.CustomUserDetailsBusiness;
import com.example.demo.business.EvaluacionBusiness;
import com.example.demo.business.MaterialEducativoBusiness;
import com.example.demo.business.UsuarioBusiness;
import com.example.demo.entity.MaterialEducativo;
import com.example.demo.entity.Usuario;
import com.example.demo.entity.Evaluacion;
import com.example.demo.repository.MaterialEducativoRepository;

import org.springframework.security.core.annotation.AuthenticationPrincipal;
import org.springframework.security.core.userdetails.UserDetails; //Agregado 13-4

import java.util.List; //Agregador 4-4
import java.util.Optional;


@Controller
public class ConcursanteController {

    @Autowired
    MaterialEducativoBusiness materialEducativoBusiness;

    @Autowired
    UsuarioBusiness usuarioBusiness;

    @Autowired
    EvaluacionBusiness evaluacionBusiness;

    private MaterialEducativo materialGanador;


    @GetMapping("/panelSubirMaterial")
    public String mostrarPanelSubirMaterial() {
        return "panelSubirMaterial";
    }

    

    @PostMapping("/subirMaterial")
    public String subirMaterial(@RequestParam("documento") MultipartFile archivo,
                                @RequestParam("nombreMaterial") String nombreMaterial,
                                @RequestParam("descripcion") String descripcion) {
        try {
            if (!archivo.isEmpty()) {
                String rutaAlmacenamiento = "C:\\Users\\lauta\\OneDrive\\Escritorio\\Workspace\\proyectoConcurso-main\\demo\\src\\main\\resources\\static\\img";
                File directorioAlmacenamiento = new File(rutaAlmacenamiento);
                if (!directorioAlmacenamiento.exists()) {
                    directorioAlmacenamiento.mkdirs();
                }
                String nombreArchivo = archivo.getOriginalFilename();
                File archivoGuardado = new File(directorioAlmacenamiento.getAbsolutePath() + File.separator + nombreArchivo);
                archivo.transferTo(archivoGuardado);

                MaterialEducativo nuevoMaterial = materialEducativoBusiness.crearNuevoMaterial();
                materialEducativoBusiness.actualizarMaterialEducativo(nuevoMaterial.getIdMaterial(), nombreMaterial, descripcion, rutaAlmacenamiento + File.separator + nombreArchivo);

                // Obtener el usuario desde la base de datos
                Authentication auth = SecurityContextHolder.getContext().getAuthentication();
                String mailUsuario = auth.getName(); // Suponiendo que el nombre de usuario es el email

                Usuario usuario = usuarioBusiness.obtenerPorMail(mailUsuario);

                // Asignar el id del material al usuario
                usuario.setMaterialEducativo(nuevoMaterial);
                // Guardar el usuario actualizado en la base de datoss
                usuarioBusiness.asignarMaterialEducativo(usuario, nuevoMaterial);

                return "redirect:/inicioConcursante";
            } else {
                return "redirect:/error";
            }
        } catch (IOException e) {
            // Manejo de la excepción Ioexception.
            e.printStackTrace();
            return "redirect:/error";
        }
    }

    @GetMapping("/panelPublicaciones.html")
    public String mostrarMaterialesPublicados(Model model) {
        List<MaterialEducativo> materialesPublicados = materialEducativoBusiness.obtenerTodosLosMaterialesPublicados();
        model.addAttribute("materialesPublicados", materialesPublicados);
        Optional<MaterialEducativo> materialGanador = materialesPublicados.stream()
            .filter(MaterialEducativo::isEsGanador)
            .findFirst();

        if (materialGanador.isPresent()) {
        model.addAttribute("materialGanador", materialGanador.get());}
        
        Authentication auth = SecurityContextHolder.getContext().getAuthentication();
        if (auth != null) {
            String rolUsuario = auth.getAuthorities().toString();
            model.addAttribute("rolUsuario", rolUsuario);
        }
        return "panelPublicaciones.html";
    }

    @PostMapping("/publicarGanador")
        public String publicarGanador(@RequestParam("idMaterial") Long idMaterial, Model model) {
            materialGanador = materialEducativoBusiness.obtenerMaterialPorId(idMaterial);
            materialEducativoBusiness.establecerGanador(materialGanador);
            return "redirect:/panelPublicaciones.html";
        }

    @PostMapping("/panelPublicaciones.html")
    public String asignarMeGusta(Long idMaterial) {
         // Obtiene la autenticación actual (el usuario logueado en el momento)
        Authentication authentication = SecurityContextHolder.getContext().getAuthentication();
        String usuarioActual = authentication.getName();

        // Verifica si el usuario ya ha dado "like" al material
        if (materialEducativoBusiness.usuarioYaDioLike(usuarioActual, idMaterial)) {
        // Si ya dio "like", redirige a la página con un mensaje de error
            return "redirect:/panelPublicaciones.html?error=YaHasDadoLike";  
        }

        // Si el usuario no ha dado "like", llama al servicio para sumar un "Me gusta" al material educativo
        materialEducativoBusiness.sumarMeGusta(idMaterial);

        // Guarda en la memoria que el usuario actual ha dado "like" al material
        materialEducativoBusiness.guardarLikeEnMemoria(usuarioActual, idMaterial);
        
        // Redirige a la página de panel de publicaciones
        return "redirect:/panelPublicaciones.html";  
    }

    @GetMapping("/evaluacionFinal")
    public String panelEvaluacionFinal(Model model) {
        // Obtener el usuario autenticado(logueado)
        Authentication authentication = SecurityContextHolder.getContext().getAuthentication();
        String mailConcursante = authentication.getName();

        // Obtener el ID del material educativo del usuario concursante
        Usuario usuario = usuarioBusiness.findUsuarioByMail(mailConcursante);
        if (usuario.getMaterialEducativo() == null) {
            model.addAttribute("mensaje", "Aún no has subido ningún material educativo.");
            return "usuarioSinMaterial"; 
        }
        
        // Obtener toda la información relacionada con el material educativo
        MaterialEducativo material = materialEducativoBusiness.obtenerMaterialPorId(usuario.getMaterialEducativo().getIdMaterial());
        //usamos try y catch para manejar los errores de los usuarios que aun no tienen un evaluador asignado
        try {
            // Obtener todas las evaluaciones relacionadas con el material educativo que buscamoss por id
            Evaluacion evaluacion = evaluacionBusiness.obtenerEvaluacionPorIdMaterial(material.getIdMaterial());
            model.addAttribute("material", material);
            model.addAttribute("evaluacion", evaluacion);
            model.addAttribute("usuarioNombre", usuario.getApellidoNombre());
            return "evaluacionFinal";
            } catch (RuntimeException e) {
            return "usuarioSinEvaluador";
            }

        }

    
    @GetMapping("/usuarioSinMaterial")
    public String mensajeSinMaterial() {
        return "usuarioSinMaterial";
        }

    @GetMapping("/usuarioSinEvaluador.html")
    public String mensajeSinEvaluador() {
        return "usuarioSinEvaluador";
        }

    @GetMapping("/evaluadorSinEvaluacion")
    public String mensajeSinEvaluacion() {
        return "evaluadorSinEvaluacion";
        }
    }
    



    
