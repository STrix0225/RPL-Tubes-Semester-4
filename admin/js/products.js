// Saat tombol edit ditekan
$('#productsTable').on('click', '.edit-btn', function () {
  const id = $(this).data('id');

  $.post('editProduct.php', { product_id: id }, function (res) {
    if (res.success) {
      const p = res.data;
      $('#editProductId').val(p.product_id);
      $('#editProductName').val(p.product_name);
      $('#editProductBrand').val(p.product_brand);
      $('#editProductCategory').val(p.product_category);
      $('#editProductColor').val(p.product_color);
      $('#editProductDescription').val(p.product_description);
      $('#editProductPrice').val(p.product_price);
      $('#editProductDiscount').val(p.product_discount);
      $('#editProductCriteria').val(p.product_criteria);

      // Set image previews
      const basePath = '../../Customer/gems-customer-pages/images/';
      if (p.product_image1) {
        $('#preview_image1').attr('src', basePath + p.product_image1).show();
      }
      if (p.product_image2) {
        $('#preview_image2').attr('src', basePath + p.product_image2).show();
      }
      if (p.product_image3) {
        $('#preview_image3').attr('src', basePath + p.product_image3).show();
      }

      $('#editProductModal').modal('show');
    } else {
      alert('Failed to fetch product data.');
    }
  }, 'json');
});

// Saat form edit disubmit
$('#editProductForm').on('submit', function (e) {
  e.preventDefault();
  
  const formData = new FormData(this);
  
  $.ajax({
    url: 'editProduct.php',
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    dataType: 'json',
    success: function (res) {
      if (res.success) {
        $('#editProductModal').modal('hide');
        alert(res.message);
        location.reload();
      } else {
        alert(res.message);
      }
    },
    error: function (xhr) {
      alert('Error while updating product. ' + xhr.responseText);
    }
  });
});

// Image preview functionality
$('input[type="file"]').change(function() {
  const previewId = 'preview_' + $(this).attr('id');
  const preview = $('#' + previewId);
  const file = this.files[0];
  
  if (file) {
    const reader = new FileReader();
    
    reader.onload = function(e) {
      preview.attr('src', e.target.result).show();
    }
    
    reader.readAsDataURL(file);
  } else {
    preview.hide();
  }
}); // Ditutup di sini

// Delete button functionality
$(document).ready(function() {
  // Gunakan event delegation untuk tombol delete yang mungkin dimuat secara dinamis
  $('#productsTable').on('click', '.delete-btn', function() {
    const id = $(this).data('id');
    $('#confirmDelete').attr('href', 'listProducts.php?delete=' + id);
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
  });
});