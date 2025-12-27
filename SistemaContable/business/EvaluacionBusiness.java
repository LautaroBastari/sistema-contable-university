package com.example.demo.business;

import java.util.List;
import java.util.Optional;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Bean;
import org.springframework.security.crypto.bcrypt.BCrypt;
import org.springframework.security.crypto.factory.PasswordEncoderFactories;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Service;

import com.example.demo.entity.Evaluacion;
import com.example.demo.entity.MaterialEducativo;
import com.example.demo.entity.Rol;
import com.example.demo.entity.Usuario;
import com.example.demo.repository.MaterialEducativoRepository;
import com.example.demo.repository.UsuarioRepository;
import com.example.demo.repository.EvaluacionRepository;


@Service
public class EvaluacionBusiness {

    @Autowired
    EvaluacionRepository evaluacionRepository;

    @Autowired
    MaterialEducativoRepository materialEducativoRepository;

    @Autowired
    UsuarioRepository usuarioRepository;


    public Evaluacion crearNuevaEvaluacion (){
        Evaluacion nuevaEvaluacion = new Evaluacion();
        return evaluacionRepository.save(nuevaEvaluacion);
    }

    public Evaluacion guardarEvaluacion(Long id_material_educativo, String mailEvaluador) {
        Evaluacion nuevaEvaluacion = new Evaluacion();
        
        // Obtener el material educativo por su ID y asignarlo a la evaluación
        MaterialEducativo material = materialEducativoRepository.findById(id_material_educativo).orElseThrow(() -> new RuntimeException("Material educativo no encontrado"));
        nuevaEvaluacion.setMaterial(material);
    
        // Obtener el evaluador por su email y asignarlo a la evaluación
        Usuario evaluador = usuarioRepository.findByMail(mailEvaluador);
        if (evaluador == null) {
            throw new RuntimeException("Evaluador no encontrado");
        }
        nuevaEvaluacion.setEvaluador(evaluador);
    
        // Guardar la evaluación en la base de datos
        return evaluacionRepository.save(nuevaEvaluacion);
    }

    public Evaluacion obtenerEvaluacion(String mailEvaluador){
        Optional<Evaluacion> evaluacionOptional = evaluacionRepository.findByEvaluadorMail(mailEvaluador);
        return evaluacionOptional.orElseThrow(() -> new RuntimeException("El usuario evaluador no tiene materiales asignados"));
    }

    public void guardarEvaluacion(Evaluacion evaluacion) {
        evaluacionRepository.save(evaluacion);
    }

    public Evaluacion obtenerEvaluacionPorMail(String mailEvaluador) {
        Optional<Evaluacion> evaluacionOptional = evaluacionRepository.findByEvaluadorMail(mailEvaluador);
        return evaluacionOptional.orElseThrow(() -> new RuntimeException("No se encontró una evaluación para el usuario evaluador con el correo: " + mailEvaluador));
    }
    
    public Evaluacion obtenerEvaluacionPorIdMaterial(Long IdMaterial) {
        Optional<Evaluacion> evaluacionOptional = evaluacionRepository.findByMaterialIdMaterial(IdMaterial);
        return evaluacionOptional.orElseThrow(() -> new RuntimeException("No se encontró una evaluación para el usuario concursante: " + IdMaterial));
    }

}
