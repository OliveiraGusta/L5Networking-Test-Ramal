function carregarRamais() {
    $.ajax({
        url: "http://localhost/L5Networking/lib/ramais.php",
        type: "GET",
        dataType: "json",
        success: function (data) {
            atualizarCartoes(data);
            preencherFiltroMembros(data);
            aplicarFiltros();

            $.ajax({
                url: "http://localhost/L5Networking/lib/updateRamais.php",
                type: "POST",
                contentType: "application/json",
                data: JSON.stringify(data),
                error: function (response) {
                    console.error("Erro ao salvar no banco Status:", response.status);
                }
            });
        },
        error: function (response) {
            console.error("Erro ao buscar os dados Status code:", response.status);
        }
    });
}

function atualizarCartoes(data) {
    $('#cartoes').empty();
    data.forEach(item => {
        $('#cartoes').append(`
            <div class="cartao cartao-${item.status}">
                <div class="cartao-phone">
                    <img id="phoneSvg" src="./assets/icons/phone-${item.status}.svg" alt="Telefone ${item.status}" />
                    <div>${item.nome}/${item.ramal}</div>
                </div>
                <div class="cartao-ip-host">${item.ipHost}:${item.porta}</div>
                <div class="cartao-status">${item.status}</div>
                <div class="cartao-membro">
                    <img id="userProfile" src="./assets/icons/userIcon.png" alt="Ícone do usuário" />
                    <div>${item.membro}</div>
                </div>
                <span class="${item.status} icone-posicao"></span>
            </div>
        `);
    });
}

//Filtro por membro
function preencherFiltroMembros(data) {
    const membroFilter = document.getElementById('membroFilter');
    const membrosUnicos = new Set(data.map(item => item.membro).filter(Boolean));

    membroFilter.innerHTML = '<option value="">Filtrar por membro</option>';
    membrosUnicos.forEach(membro => {
        const option = document.createElement('option');
        option.value = membro.toLowerCase();
        option.textContent = membro;
        membroFilter.appendChild(option);
    });
}
function filtrarPorMembro() {
    const selectedMembro = document.getElementById('membroFilter').value.toLowerCase();
    const cartoes = document.querySelectorAll('#cartoes .cartao');

    cartoes.forEach(cartao => {
        const membro = cartao.querySelector('.cartao-membro div').textContent.toLowerCase();
        cartao.style.display = !selectedMembro || membro === selectedMembro ? '' : 'none';
    });
}

//Filtro por Status
function filtrarPorStatus() {
    const selectedStatus = document.getElementById('statusFilter').value.toLowerCase();
    const cartoes = document.querySelectorAll('#cartoes .cartao');

    cartoes.forEach(cartao => {
        const status = cartao.querySelector('.cartao-status').textContent.toLowerCase();
        cartao.style.display = !selectedStatus || status === selectedStatus ? '' : 'none';
    });
}

//Filtros
function aplicarFiltros() {
    const selectedMembro = document.getElementById('membroFilter').value.toLowerCase();
    const selectedStatus = document.getElementById('statusFilter').value.toLowerCase();
    const searchValue = document.getElementById('ramalNameFilter').value.toLowerCase();
    const cartoes = document.querySelectorAll('#cartoes .cartao');

    cartoes.forEach(cartao => {
        const membro = cartao.querySelector('.cartao-membro div').textContent.toLowerCase();
        const status = cartao.querySelector('.cartao-status').textContent.toLowerCase();
        const nomeRamal = cartao.querySelector('.cartao-phone div').textContent.toLowerCase();

        const correspondeMembro = !selectedMembro || membro === selectedMembro;
        const correspondeStatus = !selectedStatus || status === selectedStatus;
        const correspondeBusca = !searchValue || nomeRamal.includes(searchValue);

        cartao.style.display = correspondeMembro && correspondeStatus && correspondeBusca ? '' : 'none';
    });
}
document.getElementById('membroFilter').addEventListener('change', aplicarFiltros);
document.getElementById('statusFilter').addEventListener('change', aplicarFiltros);
document.getElementById('ramalNameFilter').addEventListener('input', aplicarFiltros);

carregarRamais();
setInterval(carregarRamais, 10000);
