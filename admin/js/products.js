 $(document).ready(function () {
    $('#productsTable').DataTable({
      pageLength: 5,
      lengthMenu: [5, 10, 25, 50, 100], // opsi dropdown untuk user
      order: [[1, 'asc']], // urutkan berdasarkan kolom ID
      columnDefs: [
        { orderable: false, targets: [0, 6, 9] } // non-urutkan kolom No, Image, Tools
      ]
    });
  });

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

      // Set radio button criteria
      if (p.product_criteria === 'Favorite') {
        $('#editCriteriaFavorite').prop('checked', true);
      } else {
        $('#editCriteriaNonFavorite').prop('checked', true);
      }

      // Set image previews
      const basePath = '../../Customer/gems-customer-pages/images/';
      for (let i = 1; i <= 4; i++) {
        if (p[`product_image${i}`]) {
          $(`#preview_image${i}`).attr('src', basePath + p[`product_image${i}`]).show();
        } else {
          $(`#preview_image${i}`).hide();
        }
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

// Preview gambar
$('input[type="file"]').on('change', function () {
  const previewId = 'preview_' + $(this).attr('id');
  const preview = $('#' + previewId);
  const file = this.files[0];

  if (file) {
    const reader = new FileReader();
    reader.onload = function (e) {
      preview.attr('src', e.target.result).show();
    };
    reader.readAsDataURL(file);
  } else {
    preview.hide();
  }
});

// Konfirmasi delete
$('#productsTable').on('click', '.delete-btn', function () {
  const id = $(this).data('id');
  $('#confirmDelete').attr('href', 'listProducts.php?delete=' + id);
  const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
  deleteModal.show();
});
