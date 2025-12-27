package com.example.demo.entity;

import jakarta.persistence.*;

@Entity
public class Evaluacion {
    
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "id_evaluacion")
    private Long idEvaluacion;

    @OneToOne
    @JoinColumn(name = "mailIdEvaluador", referencedColumnName = "mail") 
    private Usuario evaluador;
    
    @OneToOne
    @JoinColumn (name = "id_material")
    private MaterialEducativo material;

    private String evaluacion;


    
    public Long getIdEvaluacion() {
        return this.idEvaluacion;
    }
    

    public Usuario getEvaluador() {
        return this.evaluador;
    }

    public void setEvaluador(Usuario evaluador) {
        this.evaluador = evaluador;
    }


    public MaterialEducativo getMaterial() {
        return this.material;
    }

    public void setMaterial(MaterialEducativo material) {
        this.material = material;
    }

    public String getEvaluacion() {
        return evaluacion;
    }

    public void setEvaluacion(String evaluacion) {
        this.evaluacion = evaluacion;
    }

}
