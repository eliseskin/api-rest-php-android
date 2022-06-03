<?php

require_once('./database.php');

class TienditaProductos
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
    $producto = $_POST['producto'];
    $precio = $_POST['precio'];
    $descripcion = $_POST['descripcion'];

    $con = $this->connect();
    if ($con != null) {
      $sql = "INSERT INTO productos (producto, precio, descripcion) VALUES ('$producto', '$precio', '$descripcion')";
      $result = $con->query($sql);
      if ($result = TRUE) {
        print(json_encode(array('code' => 1, 'message' => 'Producto creado correctamente')));
      } else {
        print(json_encode(array('code' => 2, 'message' => 'Error al crear el producto')));
      }
      $con->close();
    } else {
      print(json_encode(array('code' => 3, 'message' => 'Error al conectar con la base de datos')));
    }
  }

  public function update()
  {
    $id = $_POST['id'];
    $producto = $_POST['producto'];
    $precio = $_POST['precio'];
    $descripcion = $_POST['descripcion'];

    $con = $this->connect();
    if ($con != null) {
      $sql = "UPDATE productos SET producto = '$producto', precio = '$precio', descripcion = '$descripcion' WHERE id = '$id'";
      $result = $con->query($sql);
      if ($result = TRUE) {
        print(json_encode(array('code' => 1, 'message' => 'Producto actualizado correctamente')));
      } else {
        print(json_encode(array('code' => 2, 'message' => 'Error al actualizar el producto')));
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
      $sql = "DELETE FROM productos WHERE id = '$id'";
      $result = $con->query($sql);
      if ($result = TRUE) {
        print(json_encode(array('code' => 1, 'message' => 'Producto eliminado correctamente')));
      } else {
        print(json_encode(array('code' => 2, 'message' => 'Error al eliminar el producto')));
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
      $result = $con->query(Constants::$SQL_SELECT_ALL_PRODUCTOS);
      if ($result->num_rows > 0) {
        $productos = array();
        while ($row = $result->fetch_array()) {
          array_push($productos, array('id' => $row['id'], 'producto' => $row['producto'], 'precio' => $row['precio'], 'descripcion' => $row['descripcion']));
        }
        print(json_encode(array('code' => 1, 'message' => 'Productos obtenidos correctamente', 'result' => $productos)));
      } else {
        print(json_encode(array('code' => 0, 'message' => 'No hay productos')));
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

    $sql = "SELECT * FROM productos WHERE producto LIKE '%$query%' LIMIT $limit OFFSET $start";
    $con = $this->connect();
    if ($con != null) {
      $result = $con->query($sql);
      if ($result->num_rows > 0) {
        $productos = array();
        while ($row = $result->fetch_array()) {
          array_push($productos, array('id' => $row['id'], 'producto' => $row['producto'], 'precio' => $row['precio'], 'descripcion' => $row['descripcion']));
        }
        print(json_encode(array('code' => 1, 'message' => 'Productos obtenidos correctamente', 'result' => $productos)));
      } else {
        print(json_encode(array('code' => 0, 'message' => 'No hay productos')));
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

$u = new TienditaProductos();
$u->handleRequest();
