package com.example.demo.uploadingFiles.storage;

import org.springframework.boot.context.properties.ConfigurationProperties;
import org.springframework.context.annotation.Configuration;

@ConfigurationProperties("storage")
@Configuration
public class StorageProperties {

	/**
	 * Folder location for storing files
	 */
	private String location = "C:\\Users\\lauta\\OneDrive\\Escritorio\\Workspace\\proyectoConcurso-main\\demo\\src\\main\\resources\\static\\img";

	public String getLocation() {
		return location;
	}

	public void setLocation(String location) {
		this.location = location;
	}

}