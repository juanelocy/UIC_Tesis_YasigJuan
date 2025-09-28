<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Chat con IA (Gemini)</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f0f2f5; }
    .chat-container { height: 70vh; overflow-y: auto; padding: 15px; }
    .msg-user {
      max-width: 75%;
      margin-left: auto;
      background-color: #0d6efd;
      color: white;
      border-radius: 1rem;
      padding: 10px 15px;
      margin-bottom: 10px;
      white-space: pre-wrap;
    }
    .msg-ia {
      max-width: 75%;
      margin-right: auto;
      background-color: #ffffff;
      border: 1px solid #dee2e6;
      border-radius: 1rem;
      padding: 10px 15px;
      margin-bottom: 10px;
      white-space: pre-wrap;
    }
  </style>
</head>
<body>
<div class="container py-4">
  <div class="card shadow">
    <div class="card-header bg-primary text-white">
      <h4 class="mb-0">💬 Chat con IA (Gemini)</h4>
    </div>
    <div class="chat-container card-body bg-light" id="chat">
      <?php
      if (!isset($_SESSION['chat'])) $_SESSION['chat'] = [];

      // Permitir solo etiquetas seguras que Gemini devuelve
      $allowed_tags = "<br><b><strong><i><em><ul><ol><li><h1><h2><h3><p>";

      foreach ($_SESSION['chat'] as $msg) {
        $texto = nl2br(strip_tags($msg['text'], $allowed_tags));

        if ($msg['role'] === 'Tú') {
          echo "<div class='msg-user'>$texto</div>";
        } else {
          echo "<div class='msg-ia'>$texto</div>";
        }
      }
      ?>
    </div>
    <div class="card-footer">
      <form method="post" action="procesar.php" class="d-flex">
        <input type="text" name="prompt" class="form-control me-2" placeholder="Escribe tu mensaje..." required>
        <button class="btn btn-success">Enviar</button>
      </form>
    </div>
  </div>
</div>

<script>
  // Auto-scroll al final del chat
  const chatBox = document.getElementById('chat');
  chatBox.scrollTop = chatBox.scrollHeight;
</script>

</body>
</html>
