<?php
/*
 * =============================================
 * ARQUIVO: cadastro_artista.php (versão 3 — + editar/excluir)
 * =============================================
 */
include('valida_sessao.php');
include('conexao.php');

$mensagem = null;
$class    = null;

// -----------------------------------------------
// CREATE / UPDATE
// -----------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id     = $_POST['id']     ?? '';
    $nome   = $_POST['nome']   ?? '';
    $genero = $_POST['genero'] ?? '';
    $pais   = $_POST['pais']   ?? '';

    if ($id) {
        // UPDATE: o formulário trouxe um id oculto → estamos editando
        $stmt = $conn->prepare("UPDATE artistas SET nome=?, genero=?, pais=? WHERE id=?");
        $stmt->bind_param("sssi", $nome, $genero, $pais, $id);
        $mensagem = "Artista atualizado com sucesso!";
    } else {
        // INSERT: sem id → é um cadastro novo
        $stmt = $conn->prepare("INSERT INTO artistas (nome, genero, pais) VALUES (?,?,?)");
        $stmt->bind_param("sss", $nome, $genero, $pais);
        $mensagem = "Artista cadastrado com sucesso!";
    }
    $class = $stmt->execute() ? "success" : "error";
    if ($class === "error") $mensagem = "Erro: " . $stmt->error;
    $stmt->close();
}

// -----------------------------------------------
// DELETE
// -----------------------------------------------
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']); // intval() garante número inteiro (evita SQL Injection)

    // Regra de negócio: não excluir artista com músicas cadastradas
    $chk = $conn->prepare("SELECT COUNT(*) AS total FROM musicas WHERE artista_id = ?");
    $chk->bind_param("i", $id);
    $chk->execute();
    $total = $chk->get_result()->fetch_assoc()['total'];
    $chk->close();

    if ($total > 0) {
        $mensagem = "Não é possível excluir: artista possui músicas cadastradas.";
        $class    = "error";
    } else {
        $stmt = $conn->prepare("DELETE FROM artistas WHERE id = ?");
        $stmt->bind_param("i", $id);
        $class    = $stmt->execute() ? "success" : "error";
        $mensagem = $class === "success" ? "Artista excluído!" : "Erro: " . $stmt->error;
        $stmt->close();
    }
}

// -----------------------------------------------
// READ (edição e listagem)
// -----------------------------------------------
$artista = null;
if (isset($_GET['edit_id'])) {
    $eid  = intval($_GET['edit_id']);
    $stmt = $conn->prepare("SELECT * FROM artistas WHERE id = ?");
    $stmt->bind_param("i", $eid);
    $stmt->execute();
    $artista = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$artistas = $conn->query("SELECT * FROM artistas ORDER BY nome");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $artista ? 'Editar Artista' : 'Novo Artista'; ?> — Sistema Musical</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 100 100%27><text y=%27.9em%27 font-size=%2790%27>🎵</text></svg>">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">

    <div class="page-header-bar">
        <h2><?php echo $artista ? '✏️ Editar Artista' : '🎤 Novo Artista'; ?></h2>
        <div class="header-actions">
            <a href="index.php" class="btn-back">⬅ Voltar ao Menu</a>
        </div>
    </div>

    <?php if (isset($mensagem)): ?>
        <div class="message <?php echo $class; ?>">
            <?php echo $class === 'success' ? '✅' : '⚠️'; ?> <?php echo $mensagem; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <!-- Campo oculto: carrega o ID quando estamos editando -->
        <input type="hidden" name="id" value="<?php echo $artista['id'] ?? ''; ?>">

        <div class="form-grid">

            <div class="form-group">
                <label for="nome">Nome do Artista <span style="color:var(--red)">*</span></label>
                <input type="text" id="nome" name="nome"
                       value="<?php echo htmlspecialchars($artista['nome'] ?? ''); ?>"
                       required placeholder="Ex: Djavan, Marisa Monte…">
            </div>

            <div class="form-group">
                <label for="genero">Gênero Musical</label>
                <input type="text" id="genero" name="genero"
                       value="<?php echo htmlspecialchars($artista['genero'] ?? ''); ?>"
                       placeholder="Ex: MPB, Rock, Samba…">
            </div>

            <div class="form-group">
                <label for="pais">País</label>
                <input type="text" id="pais" name="pais"
                       value="<?php echo htmlspecialchars($artista['pais'] ?? ''); ?>"
                       placeholder="Ex: Brasil, EUA…">
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">
                <?php echo $artista ? '💾 Atualizar' : '➕ Cadastrar'; ?>
            </button>
            <?php if ($artista): ?>
                <a href="cadastro_artista.php" class="btn-back">✖ Cancelar</a>
            <?php endif; ?>
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
                    <th class="col-acoes">Ações</th>
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
                            <td>
                                <div class="action-buttons">
                                    <a href="?edit_id=<?php echo $row['id']; ?>" class="btn-edit">✏️ Editar</a>
                                    <a href="?delete_id=<?php echo $row['id']; ?>"
                                       onclick="return confirm('Excluir <?php echo addslashes($row['nome']); ?>?')"
                                       class="btn-delete">🗑️</a>
                                    <a href="cadastro_musica.php?artista_id=<?php echo $row['id']; ?>"
                                       class="btn-music">🎵 Músicas</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="empty-cell">Nenhum artista cadastrado ainda.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>