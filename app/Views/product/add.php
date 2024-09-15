<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Add</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>

<body>

    <div class="container py-3">
        <form id="product_form" method="POST" action="/product">
            <div class="d-flex justify-content-between align-item-center border-bottom border-black mb-3">
                <h1>Product Add</h1>
                <div>
                    <button type="submit" class="btn btn-success rounded-0" id="save-product-btn">Save</button>
                    <a href="/" class="btn btn-secondary rounded-0" id="cancel-btn">Cancel</a>
                </div>
            </div>

            <div class="col-5">
                <div class="row mb-4">
                    <div class="col-4">
                        <label for="sku" class="form-label">SKU</label>
                    </div>
                    <div class="col-8">
                        <input required type="text" class="form-control" id="sku" name="sku">
                        <span class="text-danger d-block mt-1" id="sku-error"></span>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-4">
                        <label for="name" class="form-label">Name</label>
                    </div>
                    <div class="col-8">
                        <input required type="text" class="form-control" id="name" name="name">
                        <span class="text-danger d-block mt-1" id="name-error"></span>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-4">
                        <label for="price" class="form-label">Price ($)</label>
                    </div>
                    <div class="col-8">
                        <input required type="number" class="form-control" id="price" name="price">
                        <span class="text-danger d-block mt-1" id="price-error"></span>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-4">
                        <label for="productType" class="form-label">Product Type</label>
                    </div>
                    <div class="col-8">
                        <select required class="form-select" id="productType" name="type">
                            <option selected value="">Select Product Type</option>
                            <?php foreach ($types as $type): ?>
                                <option value="<?= htmlspecialchars($type['name'], ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($type['name'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="text-danger d-block mt-1" id="type-error"></span>
                    </div>
                </div>

                <div id="attributes-container"></div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('product_form');
            const productTypeSelect = document.getElementById('productType');
            const attributesContainer = document.getElementById('attributes-container');

            productTypeSelect.addEventListener('change', function() {
                const type = this.value;
                if (type) {
                    fetch(`/product-attributes?type=${type}`)
                        .then(response => response.text()) // Change from json() to text() for debugging
                        .then(text => {
                            try {
                                const data = JSON.parse(text); // Parse the response text into JSON
                                attributesContainer.innerHTML = '';
                                data.forEach((attribute, index) => {
                                    const div = document.createElement('div');
                                    div.classList.add('row', 'mb-4');
                                    div.id = `${attribute.name}-${index}`;
                                    div.innerHTML = `
                                        <div class="col-4">
                                            <label for="${attribute.name}" class="form-label">${attribute.label}</label>
                                        </div>
                                        <div class="col-8">
                                            <input required type="number" class="form-control" id="${attribute.name}" name="${attribute.name}">
                                        </div>
                                    `;
                                    attributesContainer.appendChild(div);
                                });
                            } catch (e) {
                                console.error('Error parsing JSON:', e);
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching attributes:', error);
                        });
                } else {
                    attributesContainer.innerHTML = '';
                }
            });

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                // Clear previous errors
                document.querySelectorAll('.text-danger').forEach(el => el.textContent = '');

                const sku = document.getElementById('sku').value;
                const name = document.getElementById('name').value;
                const price = document.getElementById('price').value;
                const type = productTypeSelect.value;
                const errors = {};

                // Client-side validation
                if (!sku) errors.sku = 'Sku is required';
                if (!name) errors.name = 'Name is required';
                if (!price) errors.price = 'Price is required';
                if (!type) errors.type = 'Product Type is required';

                // Display validation errors
                for (const [key, value] of Object.entries(errors)) {
                    document.getElementById(`${key}-error`).textContent = value;
                }

                if (Object.keys(errors).length > 0) return;

                // Server-side validation
                try {
                    await axios.post('/product-validate', {
                        name: name,
                        sku: sku,
                        price: price,
                        type: type,
                    });
                    // If no errors, submit the form
                    form.submit();
                } catch (error) {
                    if (error.response && error.response.status === 419) {
                        // Handle validation errors from the server
                        for (const [key, value] of Object.entries(error.response.data.errors)) {
                            document.getElementById(`${key}-error`).textContent = value;
                        }
                    }
                }
            });
        });
    </script>

</body>

</html>