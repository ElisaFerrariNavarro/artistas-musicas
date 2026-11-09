<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Artista — Sistema Musical</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 100 100%27><text y=%27.9em%27 font-size=%2790%27>🎵</text></svg>">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">

    <div class="page-header-bar">
        <h2>🎤 Novo Artista</h2>
        <div class="header-actions">
            <a href="index.html" class="btn-back">⬅ Voltar ao Menu</a>
        </div>
    </div>

    <!--
        FORMULÁRIO DE CADASTRO / EDIÇÃO
        enctype="multipart/form-data" já fica marcado aqui porque a Fase 2
        vai precisar dele para o upload de imagem — não muda nada visualmente.
    -->
    <form method="post" action="#" enctype="multipart/form-data">

        <div class="form-grid">

            <div class="form-group">
                <label for="nome">Nome do Artista <span style="color:var(--red)">*</span></label>
                <input type="text" id="nome" name="nome"
                       required placeholder="Ex: Djavan, Marisa Monte…">
            </div>

            <div class="form-group">
                <label for="genero">Gênero Musical</label>
                <input type="text" id="genero" name="genero"
                       placeholder="Ex: MPB, Rock, Samba…">
            </div>

            <div class="form-group">
                <label for="pais">País</label>
                <input type="text" id="pais" name="pais"
                       placeholder="Ex: Brasil, EUA…">
            </div>

            <div class="form-group">
                <label for="imagem">Foto do Artista</label>
                <input type="file" id="imagem" name="imagem" accept="image/*">
                <small>JPG ou PNG · Máximo 5 MB</small>
            </div>

        </div><!-- fim .form-grid -->

        <div class="form-actions">
            <button type="submit" class="btn-primary">➕ Cadastrar</button>
        </div>
    </form>


    <!-- -----------------------------------------------
         TABELA DE ARTISTAS (dados fixos nesta fase)
    ----------------------------------------------- -->
    <h3>📋 Artistas Cadastrados</h3>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th class="col-foto">Foto</th>
                    <th>Nome</th>
                    <th>Gênero</th>
                    <th>País</th>
                    <th class="col-acoes">Ações</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><div class="no-image">Sem<br>foto</div></td>
                    <td>Djavan</td>
                    <td>MPB</td>
                    <td>Brasil</td>
                    <td>
                        <div class="action-buttons">
                            <a href="#" class="btn-edit">✏️ Editar</a>
                            <a href="#" class="btn-delete">🗑️</a>
                            <a href="cadastro_musica.html" class="btn-music">🎵 Músicas</a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><div class="no-image">Sem<br>foto</div></td>
                    <td>Marisa Monte</td>
                    <td>MPB</td>
                    <td>Brasil</td>
                    <td>
                        <div class="action-buttons">
                            <a href="#" class="btn-edit">✏️ Editar</a>
                            <a href="#" class="btn-delete">🗑️</a>
                            <a href="cadastro_musica.html" class="btn-music">🎵 Músicas</a>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>

<?php
/*
 * =============================================
 * ARQUIVO: cadastro_artista.php (versão 2 — + cadastro)
 * =============================================
 */
include('valida_sessao.php');
include('conexao.php');

$mensagem = null;
$class    = null;

// -----------------------------------------------
// CREATE
// -----------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome   = $_POST['nome']   ?? '';
    $genero = $_POST['genero'] ?? '';
    $pais   = $_POST['pais']   ?? '';

    // Prepared statement: os "?" evitam SQL Injection
    $stmt = $conn->prepare("INSERT INTO artistas (nome, genero, pais) VALUES (?,?,?)");
    $stmt->bind_param("sss", $nome, $genero, $pais);
    $class    = $stmt->execute() ? "success" : "error";
    $mensagem = $class === "success" ? "Artista cadastrado com sucesso!" : "Erro: " . $stmt->error;
    $stmt->close();
}

$artistas = $conn->query("SELECT * FROM artistas ORDER BY nome");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Artista — Sistema Musical</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 100 100%27><text y=%27.9em%27 font-size=%2790%27>🎵</text></svg>">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">

    <div class="page-header-bar">
        <h2>🎤 Novo Artista</h2>
        <div class="header-actions">
            <a href="index.php" class="btn-back">⬅ Voltar ao Menu</a>
        </div>
    </div>

    <!-- Feedback de operações -->
    <?php if (isset($mensagem)): ?>
        <div class="message <?php echo $class; ?>">
            <?php echo $class === 'success' ? '✅' : '⚠️'; ?> <?php echo $mensagem; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="form-grid">

            <div class="form-group">
                <label for="nome">Nome do Artista <span style="color:var(--red)">*</span></label>
                <input type="text" id="nome" name="nome"
                       required placeholder="Ex: Djavan, Marisa Monte…">
            </div>

            <div class="form-group">
                <label for="genero">Gênero Musical</label>
                <input type="text" id="genero" name="genero"
                       placeholder="Ex: MPB, Rock, Samba…">
            </div>

            <div class="form-group">
                <label for="pais">País</label>
                <input type="text" id="pais" name="pais"
                       placeholder="Ex: Brasil, EUA…">
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">➕ Cadastrar</button>
        </div>
    </form>

    <h3>📋 Artistas Cadastrados</h3>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th class="col-foto">Foto</th>
                    <th>Nome</th>
                    <th>Gênero</th>
                    <th>País</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($artistas->num_rows > 0): ?>
                    <?php while ($row = $artistas->fetch_assoc()): ?>
                        <tr>
                            <td><div class="no-image">Sem<br>foto</div></td>
                            <td><?php echo htmlspecialchars($row['nome']); ?></td>
                            <td><?php echo htmlspecialchars($row['genero']) ?: '—'; ?></td>
                            <td><?php echo htmlspecialchars($row['pais'])   ?: '—'; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="empty-cell">Nenhum artista cadastrado ainda.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>