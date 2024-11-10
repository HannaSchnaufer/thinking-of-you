<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Validasi & Tabel Data dengan Pagination</title>

    <script src="https://cdn.jsdelivr.net/npm/just-validate@3.5.0/dist/just-validate.production.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; margin: 50px; background-color: #f0f0f0; }

        form { margin-bottom: 20px; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        input { width: 100%; padding: 10px; margin-top: 8px; margin-bottom: 12px; border: 1px solid #ccc; border-radius: 4px; }
        .is-invalid { border-color: red; }
        .error { color: red; font-size: 12px; margin-top: -10px; margin-bottom: 12px; }

        table { width: 100%; border-collapse: collapse; background-color: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #bf00ff; color: white; }

        .loader { width: 48px; height: 48px; border: 5px solid #FFF; border-bottom-color: #FF3D00; border-radius: 50%; display: block; margin: 16px auto; box-sizing: border-box; animation: rotation 1s linear infinite; }
        @keyframes rotation { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        #pagination { margin-top: 20px; text-align: center; }
        .pagination-button { padding: 5px 10px; margin: 0 5px; cursor: pointer; border: 1px solid #ccc; border-radius: 4px; }
        .pagination-button:disabled { background-color: #ddd; cursor: not-allowed; }
    </style>
</head>
<body>

    <form id="dataForm">
        <div>
            <input type="text" name="nik" class="nik" placeholder="Masukkan NIK">
            <p class="error nik--error"></p>
        </div>
        <div>
            <input type="text" name="name" class="name" placeholder="Masukkan Nama">
            <p class="error nama--error"></p>
        </div>
        <button type="submit">Simpan</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIK</th>
                <th>Nama</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <div id="pagination"></div>

    <span class="loader" style="display:none;"></span>

    <script>
        let currentPage = 1;
        const perPage = 5;

        function loadData(page) {
            const table = document.querySelector('table tbody');
            const loader = document.querySelector('.loader');
            loader.style.display = 'block';

            axios.get(`get-students.php?page=${page}`).then(response => {
                loader.style.display = 'none';
                const data = response.data;

                if (data.status) {
                    const students = data.students;
                    table.innerHTML = '';

                    students.forEach((student, index) => {
                        const row = `
                            <tr>
                                <td>${(page - 1) * perPage + index + 1}</td>
                                <td>${student.nik}</td>
                                <td>${student.nama}</td>
                            </tr>
                        `;
                        table.innerHTML += row;
                    });

                    displayPagination(data.currentPage, data.totalPages);
                }
            }).catch(error => console.error('Error fetching data:', error));
        }

        function displayPagination(currentPage, totalPages) {
            const paginationContainer = document.getElementById('pagination');
            paginationContainer.innerHTML = '';

            for (let i = 1; i <= totalPages; i++) {
                const button = document.createElement('button');
                button.textContent = i;
                button.classList.add('pagination-button');
                if (i === currentPage) {
                    button.disabled = true;
                }
                button.addEventListener('click', () => {
                    loadData(i);
                });
                paginationContainer.appendChild(button);
            }
        }


        const validation = new JustValidate('#dataForm', {
            errorFieldCssClass: 'is-invalid',
            errorLabelStyle: {
                color: 'red',
                fontSize: '12px',
            },
        });

        validation
            .addField('.nik', [
                { rule: 'required', errorMessage: 'NIK tidak boleh kosong' },
                { rule: 'number', errorMessage: 'NIK harus berupa angka' }
            ])
            .addField('.name', [
                { rule: 'required', errorMessage: 'Nama tidak boleh kosong' },
                { rule: 'minLength', value: 3, errorMessage: 'Nama minimal 3 karakter' }
            ])
            .onSuccess((event) => {
                event.preventDefault();

                const nik = document.querySelector('.nik').value;
                const name = document.querySelector('.name').value;

                axios.post('save-students.php', { nik, name })
                    .then(response => {
                        const res = response.data;
                        if (res.status) {
                            loadData(currentPage); 
                            event.target.reset();
                        } else {
                            alert(res.error);
                        }
                    });
            });

        loadData(currentPage);
    </script>

</body>
</html>
