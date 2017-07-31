<?

  $opciones = array('http'=>array("method" => "GET"));
  $contexto = stream_context_create($opciones);
  $json = file_get_contents('http://192.168.64.2/api/test/'.$_GET['id'], false, $contexto);
  $input= json_decode($json, true);

?>


<html>
<head>
<title>Index</title>
<style>
  table, th, td {
      border: 1px solid black;
      border-collapse: collapse;
  }
  th, td {
      padding: 5px;
      text-align: left;    
  }
</style>

  <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>

  <script>
    $(function() {
      $( document).on("click", ".delete", function(e) {
        e.preventDefault();

        var id = $(this).data("delete");
        $.ajax({
          url: 'http://192.168.64.2/api/test/' + id,
          type: 'DELETE',
          beforeSend: function() {
            $(".text").remove();
          },
          success: function(result) {
            $("#delete-" + id).parent().parent().remove();
            $( "#table" ).after( "<p class='text'>Deleted!</p>" );
          }
        });
      });

      $( document).on("click", ".update", function(e) {
        e.preventDefault();

        var data = { valor: $("#value").val(), valor2: $("#value2").val() };
        var id = $(this).data("update");
      
        $.ajax({
          url: 'http://192.168.64.2/api/test/' + id,
          type: 'PUT',
          data: JSON.stringify(data),
          dataType: 'json',
          beforeSend: function() {
            $(".text").remove();
          },
          success: function (result) {
              $( "#table" ).after( "<p class='text'>Updated!</p>" );
          },
        });

      });

    });
  </script>

</head>
<body>
  <table id="table">
    <tr>
      <th>ID</th>
      <th>Value 1</th> 
      <th>Value 2</th>
      <th>Update</th>
      <th>Delete</th>
    </tr>
    <? foreach($input as $elem): ?>
      <tr>
        <td><?=$elem['id']?></td>
        <td><input type="text" id="value" name="value" value="<?=$elem['valor']?>"></td> 
        <td><input type="text" id="value2" name="value2" value="<?=$elem['valor2']?>"></td> 
        <td><a class="update" data-update="<?=$elem['id']?>" href="#">Update</a></td>
        <td><a class="delete" id="delete-<?=$elem['id']?>" data-delete="<?=$elem['id']?>" href="#">Delete</a></td>
      </tr>
    <? endforeach; ?>
  </table>
  <hr>
  <a href="index.php">Back</a>
</body>
</html>
