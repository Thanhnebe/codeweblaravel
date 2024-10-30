// Chọn tất cả các phần tử có thuộc tính 'toast-list', 'data-choices' hoặc 'data-provider'
var elements = document.querySelectorAll("[toast-list], [data-choices], [data-provider]");

// Kiểm tra nếu có ít nhất một phần tử được chọn
if (elements.length > 0) {
    // Tải script Toastify từ CDN
    var toastifyScript = document.createElement('script');
    toastifyScript.src = 'https://cdn.jsdelivr.net/npm/toastify-js';
    document.head.appendChild(toastifyScript);

    // Tải script Choices từ đường dẫn cục bộ
    var choicesScript = document.createElement('script');
    choicesScript.src = 'http://sneakers.test/assets/theme/assets/libs/choices.js/public/assets/scripts/choices.min.js';
    document.head.appendChild(choicesScript);

    // Tải script Flatpickr từ đường dẫn cục bộ
    var flatpickrScript = document.createElement('script');
    flatpickrScript.src = 'http://sneakers.test/assets/theme/assets/libs/flatpickr/flatpickr.min.js';
    document.head.appendChild(flatpickrScript);
}
