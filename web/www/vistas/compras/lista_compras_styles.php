<style>
.article-group {
    background-color: #f8f9fa;
    transition: all 0.3s ease;
    border: 1px solid #dee2e6;
    margin-bottom: 1rem;
    padding: 1.5rem;
}

.article-group:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.article-group h5 {
    color: #4154f1;
    border-bottom: 2px solid #4154f1;
    padding-bottom: 0.5rem;
    margin-bottom: 1.5rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-control:read-only {
    background-color: #e9ecef;
}

.warning-text {
    color: #ffc107;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

/* Animación para nuevos artículos */
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.article-group {
    animation: slideDown 0.3s ease-out forwards;
}
</style>