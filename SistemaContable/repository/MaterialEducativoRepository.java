package com.example.demo.repository;

import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import com.example.demo.entity.MaterialEducativo;
import java.util.Optional;

@Repository
public interface MaterialEducativoRepository extends JpaRepository <MaterialEducativo, Long> 
{
    Optional<MaterialEducativo> findById(Long id);

    Optional<MaterialEducativo> findByEsGanador(Boolean esGanador);
}