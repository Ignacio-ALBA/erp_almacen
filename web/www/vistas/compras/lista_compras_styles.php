<?php
/**
 * Estilos específicos para el módulo de listas de compras
 */
?>
<style>
    /* Contenedor de artículos */
    #articulos_container {
        margin-top: 20px;
    }
    
    /* Grupos de artículos */
    .article-group {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }
    
    .article-group:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    /* Título del artículo */
    .article-group h5 {
        color: #0d6efd; 
        margin-bottom: 15px;
        padding-bottom: 5px;
        border-bottom: 2px solid #e9ecef;
    }
    
    /* Controles y campos */
    .article-group .form-control,
    .article-group .form-select {
        transition: border-color 0.15s ease-in-out;
    }
    
    .article-group .form-control:focus,
    .article-group .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    /* Campos de solo lectura */
    .article-group input[readonly] {
        background-color: #e9ecef;
        cursor: not-allowed;
        font-weight: bold;
        color: #495057;
    }
    
    /* Campos de resultados calculados */
    .article-group input[class*="RESULT"] {
        background-color: #e8f4ff;
        font-weight: bold;
    }
    
    /* Responsive para pantallas pequeñas */
    @media (max-width: 768px) {
        .article-group .row .col-md-6 {
            margin-bottom: 10px;
        }
    }
</style>