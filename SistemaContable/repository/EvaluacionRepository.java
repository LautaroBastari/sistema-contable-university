package com.example.demo.repository;

import org.springframework.stereotype.Repository;

import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;
import com.example.demo.entity.Evaluacion;
import com.example.demo.entity.Usuario;

import java.util.Optional;

@Repository
public interface EvaluacionRepository extends JpaRepository<Evaluacion, String> {
    Optional<Evaluacion> findByEvaluadorMail (String mail);

    Optional<Evaluacion> findByMaterialIdMaterial(Long idMaterial);

}
