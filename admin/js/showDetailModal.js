function showDetailModal(type, id) {
    // Show loading state
    const modalBody = document.getElementById('detailModalBody');
    modalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>';
    
    // Set modal title based on type
    const modalTitle = document.getElementById('detailModalLabel');
    modalTitle.textContent = type.charAt(0).toUpperCase() + type.slice(1) + ' Details';
    
    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('detailModal'));
    modal.show();
    
    // Load data via AJAX
    fetch(`../../admin/api/getDetail.php?type=${type}&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                modalBody.innerHTML = data.html;
            } else {
                modalBody.innerHTML = '<div class="alert alert-danger">Failed to load details</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            modalBody.innerHTML = '<div class="alert alert-danger">Error loading details</div>';
        });
}