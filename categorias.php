<?php

require_once('./database.php');

class TienditaCategorias
{
  public function connect()
  {
    $con = new mysqli(Constants::$DB_SERVER, Constants::$DB_USER, Constants::$DB_PASSWORD, Constants::$DB_NAME);
    if ($con->connect_error) {
      return null;
    } else {
      return $con;
    }
  }

  public function insert()
  {
    $categoria = $_POST['categoria'];

    $con = $this->connect();
    if ($con != null) {
      $sql = "INSERT INTO categorias (categoria) VALUES ('$categoria')";
      $result = $con->query($sql);
      if ($result = TRUE) {
        print(json_encode(array('code' => 1, 'message' => 'Categoria creado correctamente')));
      } else {
        print(json_encode(array('code' => 2, 'message' => 'Error al crear el categoria')));
      }
      $con->close();
    } else {
      print(json_encode(array('code' => 3, 'message' => 'Error al conectar con la base de datos')));
    }
  }

  public function update()
  {
    $id = $_POST['id'];
    $categoria = $_POST['categoria'];

    $con = $this->connect();
    if ($con != null) {
      $sql = "UPDATE categorias SET categoria = '$categoria' WHERE id = '$id'";
      $result = $con->query($sql);
      if ($result = TRUE) {
        print(json_encode(array('code' => 1, 'message' => 'Categoria actualizado correctamente')));
      } else {
        print(json_encode(array('code' => 2, 'message' => 'Error al actualizar el categoria')));
      }
      $con->close();
    } else {
      print(json_encode(array('code' => 3, 'message' => 'Error al conectar con la base de datos')));
    }
  }

  public function delete()
  {
    $id = $_POST['id'];

    $con = $this->connect();
    if ($con != null) {
      $sql = "DELETE FROM categorias WHERE id = '$id'";
      $result = $con->query($sql);
      if ($result = TRUE) {
        print(json_encode(array('code' => 1, 'message' => 'Categoria eliminado correctamente')));
      } else {
        print(json_encode(array('code' => 2, 'message' => 'Error al eliminar el categoria')));
      }
      $con->close();
    } else {
      print(json_encode(array('code' => 3, 'message' => 'Error al conectar con la base de datos')));
    }
  }

  public function select()
  {
    $con = $this->connect();
    if ($con != null) {
      $result = $con->query(Constants::$SQL_SELECT_ALL_CATEGORIAS);
      if ($result->num_rows > 0) {
        $categorias = array();
        while ($row = $result->fetch_array()) {
          array_push($categorias, array('id' => $row['id'], 'categoria' => $row['categoria']));
        }
        print(json_encode(array('code' => 1, 'message' => 'Categorias obtenidos correctamente', 'result' => $categorias)));
      } else {
        print(json_encode(array('code' => 0, 'message' => 'No hay categorias')));
      }
      $con->close();
    } else {
      print(json_encode(array('code' => 3, 'message' => 'Error al conectar con la base de datos')));
    }
  }

  public function search()
  {
    $query = $_POST['query'];
    $limit = $_POST['limit'];
    $start = $_POST['start'];

    $sql = "SELECT * FROM categorias WHERE categoria LIKE '%$query%' LIMIT $limit OFFSET $start";
    $con = $this->connect();
    if ($con != null) {
      $result = $con->query($sql);
      if ($result->num_rows > 0) {
        $categorias = array();
        while ($row = $result->fetch_array()) {
          array_push($categorias, array('id' => $row['id'], 'categoria' => $row['categoria']));
        }
        print(json_encode(array('code' => 1, 'message' => 'Categorias obtenidos correctamente', 'result' => $categorias)));
      } else {
        print(json_encode(array('code' => 0, 'message' => 'No hay categorias')));
      }
      $con->close();
    } else {
      print(json_encode(array('code' => 3, 'message' => 'Error al conectar con la base de datos')));
    }
  }

  public function handleRequest()
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action == 'INSERT') {
          $this->insert();
        } else if ($action == 'UPDATE') {
          $this->update();
        } else if ($action == 'DELETE') {
          $this->delete();
        } else if ($action == 'GET_PAGINATED') {
          $this->search();
        } else if ($action == 'GET_PAGINATED_SEARCH') {
          $this->search();
        } else {
          print(json_encode(array('code' => 4, 'message' => 'Acción no reconocida')));
        }
      } else {
        print(json_encode(array('code' => 5, 'message' => 'Acción no especificada')));
      }
    } else {
      $this->select();
    }
  }
}

$u = new TienditaCategorias();
$u->handleRequest();
