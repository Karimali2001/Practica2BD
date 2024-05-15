<!DOCTYPE html>
<html>
<head>
    <title>CRUD para Lineas y Articulos</title>
</head>
<body>
    <h1>CRUD para Lineas</h1>
    <form action="create_linea.php" method="post">
        <label for="codLinea">Codigo de Linea:</label><br>
        <input type="number" id="codLinea" name="codLinea"><br>
        <label for="descripcionL">Descripcion:</label><br>
        <input type="text" id="descripcionL" name="descripcionL"><br>
        <input type="submit" value="Crear Linea">
    </form>

    <!-- Aquí puedes agregar formularios similares para las operaciones de leer, actualizar y eliminar -->

    <h1>CRUD para Articulos</h1>
    <form action="create_articulo.php" method="post">
        <label for="codArticulo">Codigo de Articulo:</label><br>
        <input type="text" id="codArticulo" name="codArticulo"><br>
        <label for="descripcion">Descripcion:</label><br>
        <input type="text" id="descripcion" name="descripcion"><br>
        <!-- Aquí puedes agregar campos de entrada para los demás atributos de Articulos -->
        <input type="submit" value="Crear Articulo">
    </form>

    <!-- Aquí puedes agregar formularios similares para las operaciones de leer, actualizar y eliminar -->
</body>
</html>