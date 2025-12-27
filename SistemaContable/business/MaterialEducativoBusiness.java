package com.example.demo.business;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import com.example.demo.entity.MaterialEducativo;
import com.example.demo.repository.MaterialEducativoRepository;

import java.util.Optional;
import java.util.List;
import java.util.stream.Collectors;

import java.util.HashMap;
import java.util.HashSet;
import java.util.Map;
import java.util.Set;
@Service
public class MaterialEducativoBusiness {
    
    @Autowired
    MaterialEducativoRepository materialEducativoRepository;

    private Map<String, Set<Long>> likesPorUsuario = new HashMap<>();
    public MaterialEducativo crearNuevoMaterial (/*Usuario autorMaterial*/){
        MaterialEducativo nuevoMaterial = new MaterialEducativo();
        return materialEducativoRepository.save(nuevoMaterial);
    }

    public MaterialEducativo actualizarMaterialEducativo (Long idMaterial, String nuevoNombre, String nuevaDescripcion, String rutaArchivo){
        MaterialEducativo materialEducativo = materialEducativoRepository.findById(idMaterial).orElse(null);
        if (materialEducativo != null) {
            materialEducativo.setNombre(nuevoNombre);
            materialEducativo.setDescripcion(nuevaDescripcion);
            materialEducativo.setRutaArchivo(rutaArchivo);
            return materialEducativoRepository.save(materialEducativo);
        } else {
            return null;
        }
    }

    public void publicarMaterial(Long idMaterial) {
        MaterialEducativo materialEducativo = materialEducativoRepository.findById(idMaterial).orElse(null);
        if (materialEducativo != null) {
            materialEducativo.setMaterialPublicado(true);
            materialEducativoRepository.save(materialEducativo);
        }
    }

    public List<MaterialEducativo> obtenerTodosLosMateriales() {
        return materialEducativoRepository.findAll();
    }

    public List<MaterialEducativo> obtenerTodosLosMaterialesPublicados() {
        List<MaterialEducativo> todosLosMateriales = materialEducativoRepository.findAll();
        return todosLosMateriales.stream()
                .filter(material -> material.getMaterialPublicado() != null && material.getMaterialPublicado())
                .collect(Collectors.toList());
    }

    public void sumarMeGusta(Long idMaterial) {
        MaterialEducativo materialEducativo = materialEducativoRepository.findById(idMaterial).orElse(null);
        if (materialEducativo != null) {
            materialEducativo.sumarLike();
            materialEducativoRepository.save(materialEducativo);
        }
    }

    public boolean usuarioYaDioLike(String usuario, Long idMaterial) {
        // Verifica si el usuario ya ha dado "like" al material previamente
        Set<Long> likesUsuario = likesPorUsuario.getOrDefault(usuario, new HashSet<>());
        return likesUsuario.contains(idMaterial);
    }

    public void guardarLikeEnMemoria(String usuario, Long idMaterial) {
        Set<Long> likesUsuario = likesPorUsuario.getOrDefault(usuario, new HashSet<>());
        likesUsuario.add(idMaterial);
        likesPorUsuario.put(usuario, likesUsuario);

        likesUsuario.add(idMaterial);
        likesPorUsuario.put(usuario, likesUsuario);
    }

    public MaterialEducativo obtenerMaterialPorId(Long id) {
        Optional<MaterialEducativo> optionalMaterial = materialEducativoRepository.findById(id);
        return optionalMaterial.orElse(null);
    }

    Optional<MaterialEducativo> obtenerMaterialGanador() {
        return materialEducativoRepository.findByEsGanador(true);
    }
    public MaterialEducativo establecerGanador(MaterialEducativo materialEducativo){
        Optional<MaterialEducativo> materialAnteriorOptional = obtenerMaterialGanador();

        if (materialAnteriorOptional.isPresent()) {
            MaterialEducativo materialAnterior = materialAnteriorOptional.get();
            materialAnterior.setEsGanador(false); 
            materialEducativoRepository.save(materialAnterior); 
        }
        materialEducativo.setEsGanador(true);
       
        return materialEducativoRepository.save(materialEducativo);
    }

    
}