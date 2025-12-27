package com.example.demo.controller;

import java.util.List;
import java.util.stream.Collectors;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.ModelAttribute;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestParam;

import com.example.demo.business.EvaluacionBusiness;
import com.example.demo.business.MaterialEducativoBusiness;
import com.example.demo.business.UsuarioBusiness;
import com.example.demo.entity.Evaluacion;
import com.example.demo.entity.MaterialEducativo;
import com.example.demo.entity.Rol;
import com.example.demo.entity.Usuario;
import com.example.demo.repository.EvaluacionRepository;
import com.example.demo.repository.MaterialEducativoRepository;
import com.example.demo.repository.UsuarioRepository;

import org.springframework.dao.DataIntegrityViolationException;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestParam;

import org.springframework.ui.Model;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;

import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.context.SecurityContextHolder;


@Controller
public class UsuarioController {

    private Rol rol;

    @Autowired
    UsuarioBusiness usuarioBusiness;

    @Autowired
    MaterialEducativoBusiness materialEducativo;

    EvaluacionBusiness evaluacionBusiness;

    @Autowired
    MaterialEducativoRepository materialEducativoRepository;

    @Autowired
    UsuarioRepository usuarioRepository;

    @Autowired
    EvaluacionRepository evaluacionRepository;

    @Autowired
    public void UsuarioController(EvaluacionBusiness evaluacionBusiness) {
        this.evaluacionBusiness = evaluacionBusiness;
    }
    

    @GetMapping("/formularioRegistro")
    public String registro() {
        return "formularioRegistro";
    }

    @PostMapping("/formularioRegistro")
    public String altaUsuario(Usuario usuario) {
        usuarioBusiness.insertUsuario(usuario);
        return "usuarioRegistrado";  
    }

    @GetMapping("/usuarioRegistrado")
    public String usuarioRegistrado() {
        return "usuarioRegistrado";
    }

    @GetMapping("/listaUsuarios")
    public String mostrarListaDeUsuarios(Model model) {
        List<Usuario> usuarios = usuarioBusiness.obtenerTodosLosUsuarios();
        model.addAttribute("usuarios", usuarios);
        return "listaUsuarios";
    }

    @GetMapping("/asignarEvaluador.html")
    public String asignacionDeEvaluadores(@RequestParam("id_material") Long id_material_educativo, Model model) {
        List<Usuario> usuarios = usuarioBusiness.obtenerTodosLosUsuarios();
        List<Usuario> evaluadores = usuarios.stream()
            .filter(usuario -> Rol.EVALUADOR.equals(usuario.getRol()))
            .collect(Collectors.toList());
        model.addAttribute("id_material", id_material_educativo);
        model.addAttribute("evaluadores", evaluadores);
        return "asignarEvaluador";
    }
    
    @PostMapping("/guardarEvaluacion")
    public String guardarEvaluacion(@RequestParam("id_material") Long id_material_educativo, @RequestParam("mailEvaluador") String mailEvaluador, Model model, RedirectAttributes redirectAttributes) {
        try {
            Evaluacion evaluacionGuardada = evaluacionBusiness.guardarEvaluacion(id_material_educativo, mailEvaluador);
            redirectAttributes.addFlashAttribute("exitoMessage", "Asignación exitosa");
            return "seleccionMateriales.html";
        } catch (DataIntegrityViolationException e) {
            // Manejo de la excepción de violación de unicidad
            model.addAttribute("error", "Ya existe una evaluación con el mismo correo electrónico del evaluador.");
            return "asignarEvaluador"; 
        }
    }

    
    
    @PostMapping("/eliminarUsuario/{mail}")
    public String eliminarUsuario(@PathVariable String mail){
        usuarioBusiness.deleteUsuario(mail);
        return "usuarioEliminado";
    }

    @PostMapping("/formularioModificarUsuario")
    public String formularioModificarUsuario() {
        return "formularioModificarUsuario";
    }
    
    @PostMapping ("/buscarUsuario/{mail}")
    public String buscarUsario (@PathVariable String mail, Model model){
        Usuario usuario = usuarioBusiness.obtenerPorMail(mail);

        if (usuario != null) {
            model.addAttribute("usuario", usuario);
            return "formularioModificarUsuario";
        } else {
            // Puedes agregar un mensaje de error si el usuario no se encuentra
            model.addAttribute("error", "Usuario no encontrado");
            return "modificacion";
        }
    }

    @PostMapping("/actualizarUsuario")
    public String actualizarUsuario(@RequestParam String mail, @RequestParam String apellidoNombre, @RequestParam String telefono, @RequestParam Rol rol) {
        usuarioBusiness.actualizarDatosUsuario(mail, apellidoNombre, telefono, rol);
        return "usuarioActualizado"; 
    }

    @GetMapping("/usuarioActualizado")
    public String modificar(){
        return "usuarioActualizado";
    }

    @PostMapping("/publicarMaterial")
    public String publicarMaterial(@RequestParam("id_material") Long idMaterial) {
        // Llama al servicio para actualizar el campo material_publicado a true
        materialEducativo.publicarMaterial(idMaterial);
        // Redirige a la página que muestra el panel de publicaciones
        return "seleccionMateriales";
    }

    @GetMapping("/listaEvaluadores")
    public String mostrarListaDeEvaluadores(Model model) {
        List<Usuario> usuarios = usuarioBusiness.obtenerTodosLosUsuarios();
        List<Usuario> evaluadores = usuarios.stream()
            .filter(usuario -> Rol.EVALUADOR.equals(usuario.getRol()))
            .collect(Collectors.toList());
        model.addAttribute("evaluadores", evaluadores);
        return "listaEvaluadores";
    }

    @GetMapping("/listaAdministradores")
    public String mostrarListaDeAdministradores(Model model) {
        List<Usuario> usuarios = usuarioBusiness.obtenerTodosLosUsuarios();
        List<Usuario> administradores = usuarios.stream()
                .filter(usuario -> Rol.ADMINISTRADOR.equals(usuario.getRol()))
                .collect(Collectors.toList());
        model.addAttribute("administradores", administradores);
        return "listaAdministradores";
    }

    @GetMapping("/listaConcursantes")
    public String mostrarListaDeConcursantes(Model model) {
        List<Usuario> usuarios = usuarioBusiness.obtenerTodosLosUsuarios();
        List<Usuario> concursantes = usuarios.stream()
                .filter(usuario -> Rol.CONCURSANTE.equals(usuario.getRol()))
                .collect(Collectors.toList());
        model.addAttribute("concursantes", concursantes);
        return "listaConcursantes";
    }

    @GetMapping("/staff")
    public String mostrarStaff() {
        return "staff";
    }

    @GetMapping("/plantillaPerfil.html")
    public String mostrarPerfilUsuario(Model model) {
        Authentication authentication = SecurityContextHolder.getContext().getAuthentication();
        String mail = authentication.getName();
        Usuario usuario = usuarioBusiness.findUsuarioByMail(mail);
        String rolUsuario = usuario.getRol().toString();
        model.addAttribute("usuario", usuario);
        model.addAttribute("rol", rolUsuario);
        return "plantillaPerfil";
    }

}