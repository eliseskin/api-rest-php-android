<?php

require_once('./database.php');

class TienditaUsuarios
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
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];
    $nombre = $_POST['nombre'];

    $con = $this->connect();
    if ($con != null) {
      $sql = "INSERT INTO usuarios (usuario, password, nombre) VALUES ('$usuario', '$password', '$nombre')";
      $result = $con->query($sql);
      if ($result = TRUE) {
        print(json_encode(array('code' => 1, 'message' => 'Usuario creado correctamente')));
      } else {
        print(json_encode(array('code' => 2, 'message' => 'Error al crear el usuario')));
      }
      $con->close();
    } else {
      print(json_encode(array('code' => 3, 'message' => 'Error al conectar con la base de datos')));
    }
  }

  public function update()
  {
    $id = $_POST['id'];
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];
    $nombre = $_POST['nombre'];

    $con = $this->connect();
    if ($con != null) {
      $sql = "UPDATE usuarios SET usuario = '$usuario', password = '$password', nombre = '$nombre' WHERE id = '$id'";
      $result = $con->query($sql);
      if ($result = TRUE) {
        print(json_encode(array('code' => 1, 'message' => 'Usuario actualizado correctamente')));
      } else {
        print(json_encode(array('code' => 2, 'message' => 'Error al actualizar el usuario')));
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
      $sql = "DELETE FROM usuarios WHERE id = '$id'";
      $result = $con->query($sql);
      if ($result = TRUE) {
        print(json_encode(array('code' => 1, 'message' => 'Usuario eliminado correctamente')));
      } else {
        print(json_encode(array('code' => 2, 'message' => 'Error al eliminar el usuario')));
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
      $result = $con->query(Constants::$SQL_SELECT_ALL_USUARIOS);
      if ($result->num_rows > 0) {
        $usuarios = array();
        while ($row = $result->fetch_array()) {
          array_push($usuarios, array('id' => $row['id'], 'usuario' => $row['usuario'], 'password' => $row['password'], 'nombre' => $row['nombre']));
        }
        print(json_encode(array('code' => 1, 'message' => 'Usuarios obtenidos correctamente', 'result' => $usuarios)));
      } else {
        print(json_encode(array('code' => 0, 'message' => 'No hay usuarios')));
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

    $sql = "SELECT * FROM usuarios WHERE usuario LIKE '%$query%' OR nombre LIKE '%$query%' LIMIT $limit OFFSET $start";
    $con = $this->connect();
    if ($con != null) {
      $result = $con->query($sql);
      if ($result->num_rows > 0) {
        $usuarios = array();
        while ($row = $result->fetch_array()) {
          array_push($usuarios, array('id' => $row['id'], 'usuario' => $row['usuario'], 'password' => $row['password'], 'nombre' => $row['nombre']));
        }
        print(json_encode(array('code' => 1, 'message' => 'Usuarios obtenidos correctamente', 'result' => $usuarios)));
      } else {
        print(json_encode(array('code' => 0, 'message' => 'No hay usuarios')));
      }
      $con->close();
    } else {
      print(json_encode(array('code' => 3, 'message' => 'Error al conectar con la base de datos')));
    }
  }

  public function login()
  {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    $con = $this->connect();
    if ($con != null) {
      $sql = "SELECT (usuario, password) FROM usuarios WHERE usuario = '$usuario' AND password = '$password'";
      $result = $con->query($sql);
      if ($result = TRUE) {
        print(json_encode(array('code' => 1, 'message' => 'Login Correcto')));
      } else {
        print(json_encode(array('code' => 2, 'message' => 'Datos incorrectos')));
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
        } else if ($action == 'LOGIN') {
          $this->login();
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

$u = new TienditaUsuarios();
$u->handleRequest();
