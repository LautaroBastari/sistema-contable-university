package com.example.demo.entity;

import jakarta.persistence.*;
import java.io.File;

import org.springframework.web.multipart.MultipartFile;

@Entity
public class MaterialEducativo {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long idMaterial;
    private String nombre;
    private String descripcion;
    private Long cantidadLike;
    private String rutaArchivo;
    private Boolean material_publicado;
    private Boolean esGanador;
   
    public MaterialEducativo() {
        this.cantidadLike = 0L;
        this.material_publicado = false;
        this.esGanador = false; 
    }
    
    public Long getId() {
        return this.idMaterial;
    }

    public String getNombre() {
        return this.nombre;
    }

    public void setNombre(String nombre) {
        this.nombre = nombre;
    }

    public String getDescripcion() {
        return this.descripcion;
    }

    public void setDescripcion(String descripcion) {
        this.descripcion = descripcion;
    }

    public Long getIdMaterial() {
        return this.idMaterial;
    }

    public void setIdMaterial(Long idMaterial) {
        this.idMaterial = idMaterial;
    }

    public Long getCantidadLike() {
        return this.cantidadLike;
    }

    public void setCantidadLike(Long cantidadLike) {
        this.cantidadLike = cantidadLike;
    }

    public void sumarLike() {
        this.cantidadLike++;  // Aumenta en 1 el valor de cantidadLike
    }

    public String getRutaArchivo() {
        return this.rutaArchivo;
    }

    public void setRutaArchivo(String rutaArchivo) {
        File archivo = new File(rutaArchivo);
        this.rutaArchivo = "img" + File.separator + archivo.getName();
    }


    public Boolean getMaterialPublicado() {
        return material_publicado;
    }

    public void setMaterialPublicado(Boolean material_publicado) {
        this.material_publicado = material_publicado;
    }

    public Boolean isEsGanador() {
        return esGanador;
    }

    public void setEsGanador(Boolean esGanador) {
        this.esGanador = esGanador;
    }

}
