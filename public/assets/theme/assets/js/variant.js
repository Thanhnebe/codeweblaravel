let listVariant = document.querySelector('#listVariant');
let btnAddVariant = document.querySelector('.addVariant');

if (btnAddVariant) {
    btnAddVariant.addEventListener('click', function () {
        let variantElement = document.querySelector(".variant").cloneNode(true);

        let inputs = variantElement.querySelectorAll("input");

        // Xóa giá trị trong các input
        inputs.forEach(function (input) {
            if (input.type === "number") {
                input.value = "";
            }
        });

        // Kiểm tra và chỉ thêm nút xóa mới nếu không tồn tại
        if (!variantElement.querySelector('.btn-outline-danger')) {
            let btnDelete = document.createElement('input');
            btnDelete.setAttribute("type", "button");
            btnDelete.setAttribute("onclick", 'removeElement(this)');
            btnDelete.setAttribute("class", "btn btn-outline-danger");
            btnDelete.setAttribute("value", "Xóa");
            btnDelete.setAttribute("style", "height: 37px;");

            // Thêm nút xóa vào phần tử clone
            variantElement.appendChild(btnDelete);
        }

        // Thêm phần tử clone vào danh sách
        listVariant.appendChild(variantElement);
    });
}

function removeElement(btn) {
    btn.parentElement.remove();
}


function attachFileInputListener(fileInput) {
    fileInput.addEventListener('change', function(event) {
        var input = event.target;
        var file = input.files[0];
        var reader = new FileReader();

        reader.onload = function(e) {
            var img = input.nextElementSibling.nextElementSibling;
            var removeButton = img.nextElementSibling;
            img.src = e.target.result;
            img.style.display = 'block';
            input.nextElementSibling.style.display = 'none';
            removeButton.style.display = 'flex';
        };

        if (file) {
            reader.readAsDataURL(file);
        }
    });
}

function attachRemoveButtonListener(removeButton) {
    removeButton.addEventListener('click', function(event) {
        var wrapper = event.target.closest('.file-input-wrapper');
        var fileInput = wrapper.querySelector('input[name="productVariant[imageVariant][]"]');
        var img = wrapper.querySelector('img');
        var customButton = wrapper.querySelector('.custom-button');

        fileInput.value = '';
        img.src = '';
        img.style.display = 'none';
        customButton.style.display = 'flex';
        removeButton.style.display = 'none';
    });
}



// Hàm thêm sự kiện change cho các select
function addChangeEventToSelects() {
    var selects = document.querySelectorAll('.weight_id');

    selects.forEach(function (select) {
        select.addEventListener('change', function () {
            disableSelectedOptions(); // Gọi hàm disable khi có thay đổi
        });
    });
}

// Hàm disable các option đã được chọn
function disableSelectedOptions() {
    var selectedValues = Array.from(document.querySelectorAll('.weight_id')).map(function (select) {
        return select.value;
    });

    var selects = document.querySelectorAll('.weight_id');

    selects.forEach(function (select) {
        var options = select.querySelectorAll('option');
        options.forEach(function (option) {
            if (selectedValues.includes(option.value) && option.value !== "") {
                option.style.display = 'none';  // Disable các option đã được chọn
            } else {
                option.style.display = 'block'; // Enable các option chưa được chọn
            }
        });
    });
}

// Hàm kiểm tra nếu tất cả các option đã được chọn hoặc bị disable
function checkIfAllOptionsDisabled() {
    var selects = document.querySelectorAll('.weight_id');
    var allOptionsDisabled = true;

    selects.forEach(function (select) {
        var options = select.querySelectorAll('option');
        var hasEnabledOption = false;

        options.forEach(function (option) {
            if (option.style.display !== 'none' && option.value !== "") {
                hasEnabledOption = true;  // Nếu có ít nhất 1 option có thể chọn
            }
        });

        if (hasEnabledOption) {
            allOptionsDisabled = false;
        }
    });

    return allOptionsDisabled;
}

// Hàm kiểm tra nếu trọng lượng đã đạt tối đa
function checkForMaxWeight() {
    if (checkIfAllOptionsDisabled()) {
        Swal.fire({
            icon: 'error',
            title: 'Trọng lượng đã đạt tối đa',
            text: 'Tất cả trọng lượng đã được chọn, không thể thêm biến thể!',
        });
    }
}

// Gọi hàm khi trang load để gắn sự kiện change
document.addEventListener('DOMContentLoaded', function () {
    addChangeEventToSelects();
});

const addColor = document.getElementById('addBienTheMauSac');

if (addColor) {
    addColor.addEventListener('click', function () {
        // Trước khi thêm biến thể, kiểm tra nếu tất cả option đã được chọn hoặc disable
        if (checkIfAllOptionsDisabled()) {
            Swal.fire({
                icon: 'error',
                title: 'Trọng lượng đã đạt tối đa',
                text: 'Tất cả trọng lượng đã được chọn, không thể thêm biến thể!',
            });
            return; // Không cho phép thêm biến thể nếu không còn option nào
        }

        // Nếu còn option có thể chọn, thì tiếp tục thêm biến thể
        var container = document.getElementById('variantContainer');
        var box = container.querySelector('.variantColor');

        // Kiểm tra nếu không có box nào tồn tại
        if (!box) {
            var deleteNoDataBox = document.querySelector('.deleteNoData');
            if (deleteNoDataBox) {
                box = deleteNoDataBox.cloneNode(true);
                box.classList.remove('deleteNoData');
            }
        } else {
            box = box.cloneNode(true);
        }

        // Xóa input ẩn chứa id để tránh việc trùng lặp id
        var hiddenIdInput = box.querySelector('input[type="hidden"][name="productVariant[id][]"]');
        if (hiddenIdInput) {
            hiddenIdInput.remove();
        }

        // Reset các input và select
        var numberInputs = box.querySelectorAll('input[type="number"]');
        var selectedBoxs = box.querySelectorAll('.weight_id');
        var spanError = box.querySelectorAll('.text-danger');

        var oldBtnRemoveVariant = box.querySelector('input[type="button"]');
        if (oldBtnRemoveVariant) {
            oldBtnRemoveVariant.remove();
        }

        numberInputs.forEach(function (numberInput) {
            numberInput.value = ''; // Reset giá trị
        });

        selectedBoxs.forEach(function (selectedBox) {
            selectedBox.selectedIndex = 0; // Reset trạng thái select
        });

        spanError.forEach(function (span) {
            span.innerHTML = ''; // Xóa thông báo lỗi
        });

        // Tạo nút xóa mới
        let btnDelete = document.createElement('input');
        btnDelete.setAttribute("type", "button");
        btnDelete.setAttribute("onclick", 'removeElement(this)');
        btnDelete.setAttribute("class", "mb-3 btn btn-outline-danger"); // Thêm class để dễ dàng tìm
        btnDelete.setAttribute("value", "Xóa");
        btnDelete.setAttribute("style", "height: 37px;");

        // Thêm nút xóa vào box
        box.appendChild(btnDelete);

        // Thêm box clone vào container
        container.appendChild(box);

        // Sau khi clone thì gắn lại sự kiện change cho các select mới
        addChangeEventToSelects();
        disableSelectedOptions(); // Kiểm tra lại các option đã chọn
    });
}


document.querySelectorAll('input[name="productVariant[imageVariant][]"]').forEach(input => {
    attachFileInputListener(input);
});

document.querySelectorAll('.remove-button').forEach(button => {
    attachRemoveButtonListener(button);
});


{/* <script>
        function attachFileInputListener(fileInput) {
            fileInput.addEventListener('change', function(event) {
                const files = event.target.files;
                let firstBox = true;
                for (let i = 0; i < files.length; i++) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        if (firstBox) {
                            let wrapper = fileInput.closest('.file-input-wrapper');
                            let img = wrapper.querySelector('img');
                            let customButton = wrapper.querySelector('.custom-button');
                            let removeButton = wrapper.querySelector('.remove-button');

                            img.src = e.target.result;
                            img.style.display = 'block';
                            customButton.style.display = 'none';
                            removeButton.style.display = 'flex';
                            firstBox = false;
                        } else {
                            let container = document.getElementById('variantContainer');
                            let newBox = document.createElement('div');
                            newBox.classList.add('variantColor', 'd-flex', 'align-items-center');
                            newBox.innerHTML = `
                                <div class="mb-3 w-25 file-input-wrapper" style="margin-right: 18px; width: 110px !important;">
                                    <div class="custom-button" style="display: none;"><i class="nav-icon fas fa-upload"></i></div>
                                    <img src="${e.target.result}" alt="Preview Image" style="display: block;">
                                    <button class="remove-button" type="button" style="display: flex;">&times;</button>
                                </div>
                            `;
                            let removeButton = newBox.querySelector('.remove-button');
                            attachRemoveButtonListener(removeButton);
                            container.appendChild(newBox);
                        }
                    };
                    reader.readAsDataURL(files[i]);
                }
            });
        }

        function attachRemoveButtonListener(removeButton) {
            removeButton.addEventListener('click', function(event) {
                var variantColor = document.querySelectorAll('.variantColor');

                variantColor.forEach(function(element) {
                    if(variantColor.length === 1) {
                        let wrapper = event.target.closest('.file-input-wrapper');
                        let img = wrapper.querySelector('img');
                        let fileInput = wrapper.querySelector('input[type="file"]');
                        let customButton = wrapper.querySelector('.custom-button');
                        img.src = '';
                        img.style.display = 'none';
                        customButton.style.display = 'flex';
                        fileInput.value = '';
                        removeButton.style.display = 'none';
                    } else {
                        event.target.closest('.variantColor').remove();
                    }
                })
            });
        }

        document.getElementById('addAnhLienQuan').addEventListener('click', function () {
            let container = document.getElementById('variantContainer');
            let newBox = document.createElement('div');
            newBox.classList.add('variantColor', 'd-flex', 'align-items-center');
            newBox.innerHTML = `
                <div class="mb-3 w-25 file-input-wrapper" style="margin-right: 18px; width: 110px !important;">
                    <input type="file" multiple name="relatedPhotos[]" class="form-control">
                    <div class="custom-button"><i class="nav-icon fas fa-upload"></i></div>
                    <img src="#" alt="Preview Image" style="display: none;">
                    <button class="remove-button" type="button" style="display: flex;">&times;</button>
                </div>
            `;
            let fileInput = newBox.querySelector('input[type="file"]');
            let removeButton = newBox.querySelector('.remove-button');
            attachFileInputListener(fileInput);
            attachRemoveButtonListener(removeButton);
            container.appendChild(newBox);
        });

        document.querySelectorAll('input[name="relatedPhotos[]"]').forEach(input => {
            attachFileInputListener(input);
        });

        document.querySelectorAll('.remove-button').forEach(button => {
            attachRemoveButtonListener(button);
        });
    </script> */}


    // <script>
    //     function attachFileInputListener(fileInput) {
    //         $(fileInput).on('change', function (event) {
    //             const files = event.target.files;
    //             let firstBox = true;
    //             for (let i = 0; i < files.length; i++) {
    //                 let reader = new FileReader();
    //                 reader.onload = function (e) {
    //                     if (firstBox) {
    //                         let wrapper = $(fileInput).closest('.file-input-wrapper');
    //                         let img = wrapper.find('img');
    //                         let customButton = wrapper.find('.custom-button');
    //                         let removeButton = wrapper.find('.remove-button');

    //                         img.attr('src', e.target.result);
    //                         img.show();
    //                         customButton.hide();
    //                         removeButton.show();
    //                         firstBox = false;
    //                     } else {
    //                         let container = $('#variantContainer');
    //                         let newBox = $('<div>').addClass('variantColor d-flex align-items-center').html(`
    //                         <div class="mb-3 w-25 file-input-wrapper" style="margin-right: 18px; width: 110px !important;">
    //                             <div class="custom-button" style="display: none;"><i class="nav-icon fas fa-upload"></i></div>
    //                             <img src="${e.target.result}" alt="Preview Image" style="display: block;">
    //                             <button class="remove-button" type="button" style="display: flex;">&times;</button>
    //                         </div>
    //                     `);
    //                         let removeButton = newBox.find('.remove-button');
    //                         attachRemoveButtonListener(removeButton);
    //                         container.append(newBox);
    //                     }
    //                 };
    //                 reader.readAsDataURL(files[i]);
    //             }
    //         });
    //     }

    //     function attachRemoveButtonListener(removeButton) {
    //         $(removeButton).on('click', function (event) {
    //             var variantColor = $('.variantColor');

    //             variantColor.each(function (index, element) {
    //                 if (variantColor.length === 1) {
    //                     let wrapper = $(event.target).closest('.file-input-wrapper');
    //                     let img = wrapper.find('img');
    //                     let fileInput = wrapper.find('input[type="file"]');
    //                     let customButton = wrapper.find('.custom-button');
    //                     img.attr('src', '');
    //                     img.hide();
    //                     customButton.show();
    //                     fileInput.val('');
    //                     $(removeButton).hide();
    //                 } else {
    //                     $(event.target).closest('.variantColor').remove();
    //                 }
    //             });
    //         });
    //     }

    //     $('#addAnhLienQuan').on('click', function () {
    //         let container = $('#variantContainer');
    //         let newBox = $('<div>').addClass('variantColor d-flex align-items-center').html(`
    //         <div class="mb-3 w-25 file-input-wrapper" style="margin-right: 18px; width: 110px !important;">
    //             <input type="file" multiple name="relatedPhotos[]" class="form-control">
    //             <div class="custom-button"><i class="nav-icon fas fa-upload"></i></div>
    //             <img src="#" alt="Preview Image" style="display: none;">
    //             <button class="remove-button" type="button" style="display: flex;">&times;</button>
    //         </div>
    //     `);
    //         let fileInput = newBox.find('input[type="file"]');
    //         let removeButton = newBox.find('.remove-button');
    //         attachFileInputListener(fileInput);
    //         attachRemoveButtonListener(removeButton);
    //         container.append(newBox);
    //     });

    //     $('input[name="relatedPhotos[]"]').each(function () {
    //         attachFileInputListener(this);
    //     });

    //     $('.remove-button').each(function () {
    //         attachRemoveButtonListener(this);
    //     });
    // </script>
