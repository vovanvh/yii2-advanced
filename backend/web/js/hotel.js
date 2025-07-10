$(document).ready(function () {
    document.getElementById('hotel-images-input')?.addEventListener('change', function(event) {
        const files = event.target.files;
        const previewContainer = document.getElementById('image-preview-container');

        // Clear previous previews
        previewContainer.innerHTML = '';

        // Validate file count
        if (files.length > 10) {
            alert('Maximum 10 images allowed');
            event.target.value = '';
            return;
        }

        for (let i = 0; i < files.length; i++) {
            const file = files[i];

            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('File "' + file.name + '" is too large. Maximum 5MB allowed.');
                continue;
            }

            // Validate file type
            if (!file.type.startsWith('image/')) {
                alert('File "' + file.name + '" is not a valid image.');
                continue;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-md-4 col-sm-4 col-xs-6';
                col.style.marginBottom = '15px';

                col.innerHTML = `
                <div class="thumbnail">
                    <img src="${e.target.result}" alt="Preview" class="img-responsive"
                         style="height: 150px; object-fit: cover; width: 100%;">
                    <div class="caption">
                        <p><small>${file.name}</small></p>
                        <div class="form-group">
                            <input type="text" class="form-control input-sm"
                                   name="image_alt_text[]" placeholder="Alt text (optional)">
                        </div>
                        <div class="form-group">
                            <textarea class="form-control input-sm" rows="2"
                                      name="image_caption[]" placeholder="Caption (optional)"></textarea>
                        </div>
                        <label class="checkbox-inline">
                            <input type="radio" name="main_image_new" value="${i}"> Set as main
                        </label>
                    </div>
                </div>
            `;

                previewContainer.appendChild(col);
            };

            reader.readAsDataURL(file);
        }
    });

    $('#csv-import-form').on('submit', function(e) {
        e.preventDefault();

        var formData = new FormData(this);
        var submitBtn = $(this).find('button[type=\"submit\"]');

        submitBtn.prop('disabled', true).text('Importing...');

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#csv-import-modal').modal('hide');
                    $.pjax.reload({container: '#pjax-container'});

                    // Show success message
                    $('body').prepend('<div class=\"alert alert-success alert-dismissible\">' +
                        '<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>' +
                        response.message + '</div>');
                } else {
                    // Show error message
                    $('.csv-import-form').prepend('<div class=\"alert alert-danger alert-dismissible\">' +
                        '<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>' +
                        response.message + '</div>');
                }
            },
            error: function(xhr, status, error) {
                $('.csv-import-form').prepend('<div class=\"alert alert-danger alert-dismissible\">' +
                    '<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>' +
                    'An error occurred while importing the file.</div>');
            },
            complete: function() {
                submitBtn.prop('disabled', false).text('Import');
            }
        });
    });
});