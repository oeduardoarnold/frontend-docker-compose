<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Gestão de Usuários</title>
    <style>
        :root { --primary: #4a90e2; --success: #2ecc71; --danger: #e74c3c; --dark: #2c3e50; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; color: var(--dark); margin: 0; padding: 40px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        h2 { text-align: center; margin-bottom: 30px; color: var(--dark); }
        
        .form-group { display: flex; gap: 10px; margin-bottom: 30px; }
        input { flex: 1; padding: 12px 20px; border: 2px solid #eee; border-radius: 8px; outline: none; transition: 0.3s; font-size: 16px; }
        input:focus { border-color: var(--primary); }
        
        button { padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; transition: 0.3s; }
        .btn-main { background: var(--primary); color: white; }
        .btn-main:hover { opacity: 0.8; transform: translateY(-1px); }
        
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8f9fa; padding: 15px; text-align: left; border-bottom: 2px solid #eee; }
        td { padding: 15px; border-bottom: 1px solid #eee; }
        
        .actions { display: flex; gap: 10px; justify-content: flex-end; }
        .btn-edit { background: #f1c40f; color: white; padding: 6px 12px; }
        .btn-del { background: var(--danger); color: white; padding: 6px 12px; }
        .btn-edit:hover, .btn-del:hover { opacity: 0.7; }
    </style>
</head>
<body>

<div class="container">
    <h2>Gestão de Usuários</h2>
    
    <div class="form-group">
        <input type="hidden" id="userId"> <input type="text" id="userName" placeholder="Nome completo do usuário">
        <button id="btnSave" class="btn-main" onclick="handleSave()">Adicionar</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th style="text-align: right;">Ações</th>
            </tr>
        </thead>
        <tbody id="userList"></tbody>
    </table>
</div>

<script>
    const API = 'api.php/users';

    // Carregar Lista
    async function load() {
        const res = await fetch(API);
        const data = await res.json();
        document.getElementById('userList').innerHTML = data.map(u => `
            <tr>
                <td><strong>#${u.id}</strong></td>
                <td>${u.nome}</td>
                <td class="actions">
                    <button class="btn-edit" onclick="editMode(${u.id}, '${u.nome}')">Editar</button>
                    <button class="btn-del" onclick="remove(${u.id})">Excluir</button>
                </td>
            </tr>
        `).join('');
    }

    // Salvar (Cria ou Edita)
    async function handleSave() {
        const id = document.getElementById('userId').value;
        const nome = document.getElementById('userName').value;
        if(!nome) return alert("Preencha o nome!");

        const method = id ? 'PUT' : 'POST';
        const url = id ? `${API}/${id}` : API;

        await fetch(url, {
            method: method,
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ nome })
        });

        resetForm();
        load();
    }

    // Preparar formulário para edição
    function editMode(id, nome) {
        document.getElementById('userId').value = id;
        document.getElementById('userName').value = nome;
        document.getElementById('btnSave').innerText = 'Salvar Alteração';
        document.getElementById('btnSave').style.background = '#2ecc71';
        document.getElementById('userName').focus();
    }

    function resetForm() {
        document.getElementById('userId').value = '';
        document.getElementById('userName').value = '';
        document.getElementById('btnSave').innerText = 'Adicionar';
        document.getElementById('btnSave').style.background = '#4a90e2';
    }

    async function remove(id) {
        if(confirm('Deseja realmente excluir este usuário?')) {
            await fetch(`${API}/${id}`, { method: 'DELETE' });
            load();
        }
    }

    load();
</script>

</body>
</html>