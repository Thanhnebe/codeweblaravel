const inputImage = document.getElementById('inputImage');
const imagePreview = document.getElementById('imagePreview');
let files = [];

if(inputImage) {
    inputImage.addEventListener('change', function (event) {
        files = Array.from(event.target.files);
        updatePreview();
    });
}

function updatePreview() {
    imagePreview.innerHTML = '';
    files.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function (e) {
            const boxImg = document.createElement('div');
            boxImg.classList.add('box_img');

            const img = document.createElement('img');
            img.src = e.target.result;
            img.alt = file.name;

            const btnDelete = document.createElement('button');
            btnDelete.textContent = 'x';
            btnDelete.classList.add('btnDelete_image');
            btnDelete.addEventListener('click', function () {
                files.splice(index, 1);
                updateFiles();
                updatePreview();
            });

            boxImg.appendChild(img);
            boxImg.appendChild(btnDelete);
            imagePreview.appendChild(boxImg);
        };
        reader.readAsDataURL(file);
    });
}

function updateFiles() {
    const dataTransfer = new DataTransfer();
    files.forEach(file => dataTransfer.items.add(file));
    inputImage.files = dataTransfer.files;
}

document.querySelectorAll('.btnDelete_image').forEach(button => {
    button.addEventListener('click', function() {
        this.parentElement.remove();
    });
}); 