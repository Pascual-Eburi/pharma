
/*************************************************************************

  DDL app
******************************************************/
/*
  MODULOS
*/

INSERT INTO `modulos` (`id_modulo`, `nombre`, `fecha_creación`, `ultima_actualizacion`) VALUES ('', 'dashboard', current_timestamp(), current_timestamp()),
 ('', 'inventario', current_timestamp(), current_timestamp()),
 ('', 'crm', current_timestamp(), current_timestamp()),
 ('', 'facturacion', current_timestamp(), current_timestamp()),
 ('', 'contabilidad', current_timestamp(), current_timestamp()),
 ('', 'rrhh', current_timestamp(), current_timestamp()),
 ('', 'tpv', current_timestamp(), current_timestamp()),
 ('', 'general', current_timestamp(), current_timestamp()),
 ('', 'web', current_timestamp(), current_timestamp()),
 ('', 'seguridad', current_timestamp(), current_timestamp());
 
/*==================================================
  USURIOS EN GENERAL

              $q = "SELECT id_usuario, email, REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(email,'á','a'),'é','e'),'í','i'), 'ó', 'o'),'ú', 'u') as 'correct emai' from usuario where id_roll = 7 and email like 'álvaro%'";
            "UPDATE usuario SET email = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(email,'á','a'),'é','e'),'í','i'), 'ó', 'o'),'ú', 'u') as 'correct emai' from usuario where id_roll = 7 and email like 'álvaro%'";
===================================================*/
  -- actualizar direcciones de email sin acentos en las vocales
  DROP PROCEDURE IF EXISTS corregirEmails;
  DELIMITER //
  CREATE PROCEDURE corregirEmails() BEGIN
  -- Variables donde almacenar lo que nos traemos desde el SELECT
  DECLARE v_codigo VARCHAR(50); DECLARE v_email VARCHAR(100);
  -- Variable para controlar el fin del bucle
    DECLARE fin INTEGER DEFAULT 0;
  -- El SELECT que vamos a ejecutar --> elimino los espacios en blanco de los nombres
    DECLARE resultados CURSOR FOR SELECT id_usuario, REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(email,'á','a'),'é','e'),'í','i'), 'ó', 'o'),'ú', 'u') from usuario WHERE LOCATE('á', email) > 0 OR LOCATE('é', email) > 0 OR LOCATE('í', email) > 0 OR LOCATE('ó', email) > 0 OR LOCATE('ú', email) > 0;
  -- Condición de salida
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET fin=1;
    OPEN resultados;
    obtner_fila: LOOP
      FETCH resultados INTO v_codigo,v_email;
      IF fin = 1 THEN LEAVE obtner_fila; END IF;
      
      UPDATE usuario SET email = v_email WHERE usuario.id_usuario = v_codigo;


    END LOOP obtner_fila;
    CLOSE resultados;
  END //
  DELIMITER ;

  CALL corregirEmails();
 SELECT nombre FROM usuario WHERE LOCATE('á', nombre) > 0 OR LOCATE('é', nombre) > 0 OR LOCATE('í', nombre) > 0 OR LOCATE('ó', nombre) > 0 OR LOCATE('ú', nombre) > 0 LIMIT 20;

  -- SELECT count(*) FROM usuario WHERE LOCATE('á', email) > 0 OR LOCATE('é', email) > 0 OR LOCATE('í', email) > 0 OR LOCATE('ó', email) > 0 OR LOCATE('ú', email) > 0;
  -- login de ususarios en el sistema
    DROP PROCEDURE IF EXISTS loginUsuario;
    DELIMITER //
    CREATE PROCEDURE loginUsuario(IN correo VARCHAR (50),IN clave VARCHAR(255), OUT id_usuario INT, OUT id_roll INT) 
    BEGIN
      SELECT usuario.id_usuario,usuario.id_roll INTO id_usuario, id_roll FROM usuario WHERE usuario.email = correo AND usuario.clave = MD5(clave);
    END //
    DELIMITER ;
    CALL loginUsuario('kenthompson@gmail.com', 'clave', @id_usuario, @id_roll);
    SELECT @id_usuario, @id_roll; 

    -- INFO DEL USUARIO
    SELECT foto, usuario.nombre, email, roles.nombre, usuario.id_roll from usuario LEFT JOIN roles ON usuario.id_roll = roles.id_roll LIMIT 5;

    
    --
    select permisos.nombre as permiso , roles.id_roll, roles.nombre as roll, modulos.nombre as modulo, modulos.id_modulo, permisos_roll_modulo.id_permiso_roll from permisos_roll_modulo inner join modulos on permisos_roll_modulo.id_modulo = modulos.id_modulo inner join permisos on permisos_roll_modulo.id_permiso = permisos.id_permiso inner join roles on permisos_roll_modulo.id_roll = roles.id_roll;

    -- SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = 'catalogofarmacia';
-- SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'tbl_name' AND table_schema = 'db_name' AND column_name LIKE 'id_usuario';

-- select table_name from information_schema.tables where table_name in (select table_name from information_schema.tables WHERE EXISTS (select COLUMN_NAME FROM information_schema.colums WHERE table_schema = 'catalogofarmacia' AND COLUMN_NAME LIKE 'id_usuario')
);

-- select COLUMN_NAME FROM information_schema.colums WHERE table_schema = 'catalogofarmacia' AND COLUMN_NAME LIKE 'id_usuario';

SELECT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = 'catalogofarmacia' AND column_name LIKE 'id_usuario';

    DROP PROCEDURE IF EXISTS prueba;
    DELIMITER //
    CREATE PROCEDURE prueba( IN usuario CHAR (4)) BEGIN
    -- CAMPOS
    DECLARE @nombre_tabla VARCHAR(25); 
    -- FIN 
    DECLARE fin INTEGER DEFAULT 0;

    -- un solo empleado
    DECLARE datos CURSOR FOR  SELECT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = 'catalogofarmacia' AND column_name LIKE 'id_usuario';

    -- Condición de salida
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET fin=1;
    -- recorriendo los resultados
      OPEN datos;
      obtner_fila: LOOP
        FETCH datos INTO nombre_tabla;
        IF fin = 1 THEN LEAVE obtner_fila; END IF;
        SET @tabla = nombre_tabla;
        SELECT * from @tabla where id_usuario = usuario;
      END LOOP obtner_fila;
      CLOSE datos;

    END //
    DELIMITER ;

        SET @tabla = 'usuario';
        SELECT * from @tabla where @tala.id_usuario = 2;
/*##############################################################################
                                VENDEDORES
*########################################################################################/
  /*======================================================================================
      datosLoginVendedor(): @codigo @email @clave 
      devuelve solo los datos de login (email,password) del vendedor
    =========================================================================================*/
    DROP PROCEDURE IF EXISTS datosLoginVendedor;
    DELIMITER //
    CREATE PROCEDURE datosLoginVendedor(IN codigo INT(3), OUT email VARCHAR (90), OUT clave VARCHAR (255)) 
    BEGIN
      SELECT vendedor.email, vendedor.clave INTO email, clave FROM vendedor WHERE cod_vendedor = codigo;
    END //
    DELIMITER ;
    CALL datosLoginVendedor(1,@email,@clave);
    SELECT @email, @clave;

  /*==========================================================================================
      verificarLoginVendedor(): INOUT @email IN @clave 
      devuelve solo los datos de login (email,password) del vendedor
    ==========================================================================================*/
    DROP PROCEDURE IF EXISTS verificarLoginVendedor;
    DELIMITER //
    CREATE PROCEDURE verificarLoginVendedor(IN correo VARCHAR (90), OUT codigo VARCHAR (4), OUT email VARCHAR(90), OUT clave VARCHAR (255)) 
    BEGIN
      SELECT vendedor.cod_vendedor,vendedor.email, vendedor.clave INTO codigo, email, clave FROM vendedor WHERE vendedor.email = correo;
    END //
    DELIMITER ;
    SET @correo = 'kenthompson@gmail.com';
    CALL verificarLoginVendedor(@correo, @codigo, @email, @clave);
    SELECT @codigo, @email,@clave; 


  /*========================================================================================
    infoVendedores(): IN @empleado
    Devuelve todo la información de los vendedores 
    # cuando al parametro de entrada @empleado tiene un valor 0 o vacio o nulo, el procedimiento devolverá la informacion de todos los vendedores
    # cuando el parametro @empleado recibe un valor numerico mayor que 0, buscara y devolvera la informacion del vendedor con cod_vendedor = @empleado 
    ====================================================================================*/

    DROP PROCEDURE IF EXISTS infoVendedores;
    DELIMITER //
    CREATE PROCEDURE infoVendedores( IN empleado CHAR (4)) BEGIN
    -- CAMPOS
    DECLARE codigo VARCHAR(3); DECLARE nombre VARCHAR(25); DECLARE  email VARCHAR(90); DECLARE calle VARCHAR(30); DECLARE fechaNacimiento DATE; DECLARE pais VARCHAR(50); DECLARE turno VARCHAR(10); DECLARE categoria VARCHAR (20); DECLARE jefe VARCHAR(25);

    -- FIN 
    DECLARE fin INTEGER DEFAULT 0;

    -- cursores
    -- un solo empleado
    DECLARE datosEmpleado CURSOR FOR  SELECT vendedor.cod_vendedor,vendedor.nombre,vendedor.email,vendedor.calle, vendedor.f_nac, pais.nombre as pais,REPLACE(REPLACE(REPLACE(vendedor.turno,'T','Tarde'),'M','Mañana'),'N','Noche') as turno,categoria.nombre as categoria, IFNULL(jefes.nombre,'-') as jefe FROM vendedor INNER JOIN pais on vendedor.pais = pais.id_pais INNER JOIN categoria on vendedor.categoria = categoria.id_cat LEFT JOIN vendedor jefes on vendedor.jefe = jefes.cod_vendedor WHERE vendedor.cod_vendedor = empleado;

    -- totos los empleados
    DECLARE datosEmpleados CURSOR FOR  SELECT vendedor.cod_vendedor,vendedor.nombre,vendedor.email,vendedor.calle, vendedor.f_nac, pais.nombre as pais,REPLACE(REPLACE(REPLACE(vendedor.turno,'T','Tarde'),'M','Mañana'),'N','Noche') as turno,categoria.nombre as categoria, IFNULL(jefes.nombre,'-') as jefe FROM vendedor INNER JOIN pais on vendedor.pais = pais.id_pais INNER JOIN categoria on vendedor.categoria = categoria.id_cat LEFT JOIN vendedor jefes on vendedor.jefe = jefes.cod_vendedor;

    -- Condición de salida
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET fin=1;
    -- recorriendo los resultados
    IF empleado > 0 THEN -- si el codigo es > 0, busca el vendedor con ese codigo con el cursor datosEmpleado
      OPEN datosEmpleado;
      obtner_fila: LOOP
        FETCH datosEmpleado INTO codigo, nombre, email, calle, fechaNacimiento, pais, turno, categoria, jefe;
        IF fin = 1 THEN LEAVE obtner_fila; END IF;
        SELECT codigo, nombre, email, calle, fechaNacimiento, pais, turno, categoria, jefe;
      END LOOP obtner_fila;
      CLOSE datosEmpleado;

    ELSE -- devolvera la informacion de todos los empleados
      OPEN datosEmpleados;
      obtner_fila: LOOP
        FETCH datosEmpleados INTO codigo, nombre, email, calle, fechaNacimiento, pais, turno, categoria, jefe;
        IF fin = 1 THEN LEAVE obtner_fila; END IF;
        SELECT codigo, nombre, email, calle, fechaNacimiento, pais, turno, categoria, jefe;
      END LOOP obtner_fila;
      CLOSE datosEmpleados;

    END IF;
    -- fin resultados
    END //
    DELIMITER ;


  /*-----------------------------------------------------------------------------------
    actulizon el email y contraseña de los empleados para login de los clientes
  --------------------------------------------------------------------------------------*/
  DROP PROCEDURE IF EXISTS actualizar_email;
  DELIMITER //
  CREATE PROCEDURE actualizar_email () BEGIN
  -- Variables donde almacenar lo que nos traemos desde el SELECT
  DECLARE v_codigo VARCHAR(50); DECLARE v_email VARCHAR(50);
  -- Variable para controlar el fin del bucle
    DECLARE fin INTEGER DEFAULT 0;
  -- El SELECT que vamos a ejecutar --> elimino los espacios en blanco de los nombres
    DECLARE resultados CURSOR FOR SELECT cod_vendedor, CONCAT(LOWER(REPLACE(nombre, ' ', '')),'@gmail.com') from vendedor;
  -- Condición de salida
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET fin=1;
    OPEN resultados;
    obtner_fila: LOOP
      FETCH resultados INTO v_codigo,v_email;
      IF fin = 1 THEN LEAVE obtner_fila; END IF;
      
      UPDATE vendedor SET email = v_email, clave = MD5('clave') WHERE cod_vendedor = v_codigo;


    END LOOP obtner_fila;
    CLOSE resultados;
  END //
  DELIMITER ;

  CALL actualizar_email();

/*===============================
  DATOS LOGIN VENDEDORES
======================*/
  DROP PROCEDURE IF EXISTS generarLoginVendedor;
  DELIMITER //
  CREATE PROCEDURE generarLoginVendedor () BEGIN
  -- Variables donde almacenar lo que nos traemos desde el SELECT
  DECLARE cod_v INT; DECLARE v_nombre VARCHAR(50); DECLARE v_clave VARCHAR(255); DECLARE v_email VARCHAR(50); DECLARE v_id_login INT;
  -- Variable para controlar el fin del bucle
    DECLARE fin INTEGER DEFAULT 0;
  -- El SELECT que vamos a ejecutar --> elimino los espacios en blanco de los nombres
    DECLARE datosLogin CURSOR FOR SELECT cod_vendedor,nombre, email, clave  from empleado;
  -- Condición de salida
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET fin=1;
    OPEN datosLogin;
    obtner_fila: LOOP
      FETCH datosLogin INTO cod_v,v_nombre, v_email, v_clave;
      IF fin = 1 THEN LEAVE obtner_fila; END IF;
      CALL insertarLogin(v_nombre, v_email, v_clave,3, @id_asignado);
      SELECT @id_asignado INTO v_id_login;
      UPDATE empleado SET id_usuario = v_id_login WHERE empleado.cod_vendedor = cod_v;
    END LOOP obtner_fila;
    CLOSE datosLogin;
  END //
  DELIMITER ;

  CALL generarLoginVendedor();


-- INSERTAR LOGIN VENDEDOR
DROP PROCEDURE IF EXISTS insertarLogin;
DELIMITER //
CREATE PROCEDURE insertarLogin(IN nombre VARCHAR(50), IN email VARCHAR(50),IN clave VARCHAR(255),IN roll INT, OUT id_asignado INT )
BEGIN 
  DECLARE id_asignar INT;
  -- email, clave, roll
  INSERT INTO usuario (nombre, email, clave, id_roll) VALUES(nombre, email, clave, roll);
  SELECT LAST_INSERT_ID() into id_asignar;
  IF (id_asignar > 0 ) THEN
    SET id_asignado = id_asignar;
  ELSE
    SET id_asignado = 0;
  END IF;

END //
DELIMITER ;

-- sql obtencion informacion de empleados

SELECT empleados.id_usuario, empleados.email, empleados.nombre, empleados.id_roll, empleados.fecha_registro,roles.nombre as roll, empleado.cod_vendedor, calle, fecha_nacimiento, empleado.id_turno, pais as id_pais, categoria as id_categoria, id_jefe,jefes.nombre as jefe, pais.cod_pais, pais.nombre as pais, categoria.nombre, turno.nombre as turno from empleado 
inner join usuario empleados on empleado.id_usuario = empleados.id_usuario 
left join turno on empleado.id_turno = turno.id_turno 
left join pais on empleado.pais = pais.id_pais 
inner join categoria on empleado.categoria = categoria.id_cat 
left join usuario jefes on empleado.id_jefe = jefes.id_usuario
left join roles on empleados.id_roll = roles.id_roll;

SELECT empleados.id_usuario, empleados.email, empleados.nombre,roles.nombre as roll, jefes.nombre as jefe, pais.nombre as pais, categoria.nombre, turno.nombre as turno from empleado 
inner join usuario empleados on empleado.id_usuario = empleados.id_usuario 
left join turno on empleado.id_turno = turno.id_turno 
left join pais on empleado.pais = pais.id_pais 
inner join categoria on empleado.categoria = categoria.id_cat 
left join usuario jefes on empleado.id_jefe = jefes.id_usuario
left join roles on empleados.id_roll = roles.id_roll;


select e1.id_usuario, u1.nombre, u2.id_usuario, u2.nombre as nombre_jefe from empleado e1 
inner join usuario u1  on e1.id_usuario = u1.id_usuario 
left join empleado e2  on e1.jefe = e2.cod_vendedor 
left join usuario u2 on e2.id_usuario = u2.id_usuario;

select infoEmpleado.id_usuario, infoEmpleado.nombre, jefes.nombre as jefe from empleado 
inner join usuario infoEmpleado on empleado.id_usuario = infoEmpleado.id_usuario 
left join usuario jefes on empleado.id_jefe = jefes.id_usuario;

/*=====================================================================================
      CLIENTES
====================================================================================*/
-- ELIMINO CLIENTES SIN PEDIDOS Y AQUELLOS QUE TENGAN MENOS DE 5 PEDIDOS PARA REDUCIR EL Nº

  DROP PROCEDURE IF EXISTS eliminarClientesSinPedidos;
  DELIMITER //
  CREATE PROCEDURE eliminarClientesSinPedidos () BEGIN
  -- Variables donde almacenar lo que nos traemos desde el SELECT
  DECLARE v_cliente INT;  DECLARE v_usuario INT;
  -- Variable para controlar el fin del bucle
    DECLARE fin INTEGER DEFAULT 0;
  -- El SELECT que vamos a ejecutar --> elimino los espacios en blanco de los nombres
    DECLARE clientesSinPedidos CURSOR FOR SELECT id_usuario from cliente where id_cliente not in (select id_cliente from pedido);
  -- Condición de salida
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET fin=1;
    OPEN clientesSinPedidos;
    obtner_fila: LOOP
      FETCH clientesSinPedidos INTO v_usuario;
      IF fin = 1 THEN LEAVE obtner_fila; END IF;
      DELETE FROM usuario WHERE id_usuario = v_usuario;
    END LOOP obtner_fila;
    CLOSE clientesSinPedidos;
  END //
  DELIMITER ;

  CALL eliminarClientesSinPedidos();

-- LIMINAR CLIENTES CON MENOS DE 6 PEDIDOS
select count(*) from cliente where id_cliente in(select id_cliente from pedido group by id_cliente having count(*) <6);

  DROP PROCEDURE IF EXISTS elCliMenosSeisPedidos;
  DELIMITER //
  CREATE PROCEDURE elCliMenosSeisPedidos () BEGIN
  -- Variables donde almacenar lo que nos traemos desde el SELECT
  DECLARE v_usuario INT;
  -- Variable para controlar el fin del bucle
    DECLARE fin INTEGER DEFAULT 0;
  -- El SELECT que vamos a ejecutar --> 
    DECLARE cursorPedidos CURSOR FOR select id_usuario from cliente where id_cliente in(select id_cliente from pedido group by id_cliente having count(*) <6);
  -- Condición de salida
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET fin=1;
    OPEN cursorPedidos;
    obtner_fila: LOOP
      FETCH cursorPedidos INTO v_usuario;
      IF fin = 1 THEN LEAVE obtner_fila; END IF;
      DELETE FROM usuario WHERE id_usuario = v_usuario;
    END LOOP obtner_fila;
    CLOSE cursorPedidos;
  END //
  DELIMITER ;

  CALL elCliMenosSeisPedidos();


-- generar login
  DROP PROCEDURE IF EXISTS generarLoginCliente;
  DELIMITER //
  CREATE PROCEDURE generarLoginCliente () BEGIN
  -- Variables donde almacenar lo que nos traemos desde el SELECT
  DECLARE cod_v INT; DECLARE v_nombre VARCHAR(50); DECLARE v_clave VARCHAR(255); DECLARE v_email VARCHAR(50); DECLARE v_id_login INT; DECLARE duplicado INT;
  -- Variable para controlar el fin del bucle
    DECLARE fin INTEGER DEFAULT 0;
  -- El SELECT que vamos a ejecutar --> elimino los espacios en blanco de los nombres
    DECLARE datosLogin CURSOR FOR SELECT id_cliente,nombre, MD5('clave')  from cliente;
  -- Condición de salida
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET fin=1;
    OPEN datosLogin;
    obtner_fila: LOOP
      FETCH datosLogin INTO cod_v,  v_nombre, v_clave; -- v_email, v_clave;
      IF fin = 1 THEN LEAVE obtner_fila; END IF;

        SELECT COUNT(*) INTO duplicado FROM cliente where cliente.nombre IN (select distinct(nombre) from cliente group by nombre having count(*) > 1) AND cliente.nombre = v_nombre;

        IF duplicado >= 1 THEN
            SELECT CONCAT( CONCAT(LOWER(REPLACE(v_nombre, ' ', '')), cod_v ), '@gmail.com') INTO v_email;
        ELSE
          SELECT CONCAT(LOWER(REPLACE(v_nombre, ' ', '')),'@gmail.com') INTO v_email;
        END IF;
      
      CALL insertarLogin(v_nombre, v_email, v_clave,7, @id_asignado);
      SELECT @id_asignado INTO v_id_login;
      UPDATE cliente SET id_usuario = v_id_login WHERE cliente.id_cliente = cod_v;
    END LOOP obtner_fila;
    CLOSE datosLogin;
  END //
  DELIMITER ;

  CALL generarLoginCliente();

  -- INFORMACION DE LOS CLIENTES
    DROP PROCEDURE IF EXISTS infoClientes;
    DELIMITER //
    CREATE PROCEDURE infoClientes( IN id CHAR (3) ) BEGIN
    -- CAMPOS
    DECLARE id_cliente INT(3); DECLARE id_usuario INT; DECLARE foto TEXT; DECLARE nombre VARCHAR(50);  DECLARE email varchar(100);DECLARE id_provincia SMALLINT(2); DECLARE provincia VARCHAR(30); DECLARE roll varchar(30); DECLARE id_roll INT;  DECLARE fechaNacimiento DATE; DECLARE edad SMALLINT(3); DECLARE telefono VARCHAR(15); DECLARE ccaa VARCHAR(30); DECLARE pedidos SMALLINT(5); DECLARE fecha_registro DATE;
    -- FIN 
    DECLARE fin INTEGER DEFAULT 0;
    -- cursores
    -- un solo registro
    DECLARE datosCliente CURSOR FOR SELECT cliente.id_cliente, usuario.id_usuario, usuario.foto, usuario.nombre, usuario.email, cliente.provincia as id_provincia, cliente.tlfno as telefono, cliente.f_nac as fechaNacimiento, FLOOR(DATEDIFF(CURDATE(),cliente.f_nac)/365) AS edad, roles.nombre as roll, usuario.id_roll,LOWER(provincia.nombre) as provincia, LOWER(ccaa.nombre) as ccaa, count(pedido.id_cliente) as pedidos, usuario.fecha_registro from usuario LEFT JOIN roles on usuario.id_roll = roles.id_roll inner join cliente on usuario.id_usuario = cliente.id_usuario LEFT JOIN provincia on cliente.provincia = provincia.id_provincia LEFT JOIN ccaa on provincia.id_ca = ccaa.id_ca LEFT JOIN pedido on cliente.id_cliente = pedido.id_cliente WHERE cliente.id_cliente = id GROUP BY cliente.id_cliente ORDER BY usuario.fecha_registro DESC, usuario.id_usuario DESC;

    -- totos los los registros
    DECLARE datosClientes CURSOR FOR SELECT cliente.id_cliente, usuario.id_usuario, usuario.foto, usuario.nombre, usuario.email, cliente.provincia as id_provincia, cliente.tlfno as telefono, cliente.f_nac as fechaNacimiento, FLOOR(DATEDIFF(CURDATE(),cliente.f_nac)/365) AS edad, roles.nombre as roll, usuario.id_roll,LOWER(provincia.nombre) as provincia, LOWER(ccaa.nombre) as ccaa, count(pedido.id_cliente) as pedidos, usuario.fecha_registro from usuario LEFT JOIN roles on usuario.id_roll = roles.id_roll inner join cliente on usuario.id_usuario = cliente.id_usuario LEFT JOIN provincia on cliente.provincia = provincia.id_provincia LEFT JOIN ccaa on provincia.id_ca = ccaa.id_ca LEFT JOIN pedido on cliente.id_cliente = pedido.id_cliente GROUP BY cliente.id_cliente ORDER BY usuario.fecha_registro DESC, usuario.id_usuario DESC;

    -- Condición de salida
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET fin=1;
    -- recorriendo los resultados
    IF id > 0 THEN -- si el codigo es > 0, busca el reg. con ese codigo, habro su cursor
      OPEN datosCliente;
        obtner_fila: LOOP 
          FETCH datosCliente INTO id_cliente,id_usuario, foto, nombre, email, id_provincia, telefono, fechaNacimiento, edad, roll, id_roll,provincia, ccaa, pedidos, fecha_registro;
          IF fin = 1 THEN LEAVE obtner_fila; END IF;
          SELECT id_cliente,id_usuario, foto, nombre, email, id_provincia, telefono, fechaNacimiento, edad, roll, id_roll,provincia, ccaa, pedidos, fecha_registro;
        END LOOP obtner_fila;
      CLOSE datosCliente;

    ELSE -- habro el cursor que devuelve la información de todos los registros
      OPEN datosClientes;
        obtner_fila: LOOP
            FETCH datosClientes INTO id_cliente,id_usuario, foto, nombre, email, id_provincia, telefono, fechaNacimiento, edad, roll, id_roll,provincia, ccaa, pedidos, fecha_registro;
            IF fin = 1 THEN LEAVE obtner_fila; END IF;
            SELECT id_cliente,id_usuario, foto, nombre, email, id_provincia, telefono, fechaNacimiento, edad, roll, id_roll,provincia, ccaa, pedidos, fecha_registro;
        END LOOP obtner_fila;
      CLOSE datosClientes;
    END IF;
    -- fin resultados
    END //
  DELIMITER ;
CALL infoClientes(1);

/*=======================================================================
            FABRICANTE
=======================================================================*/

-- generar login
  DROP PROCEDURE IF EXISTS generarLoginFabricante;
  DELIMITER //
  CREATE PROCEDURE generarLoginFabricante () BEGIN
  -- Variables donde almacenar lo que nos traemos desde el SELECT
  DECLARE cod_v INT; DECLARE v_nombre VARCHAR(50); DECLARE v_clave VARCHAR(255); DECLARE v_email VARCHAR(50); DECLARE v_id_login INT;
  -- Variable para controlar el fin del bucle
    DECLARE fin INTEGER DEFAULT 0;
  -- El SELECT que vamos a ejecutar --> elimino los espacios en blanco de los nombres
    DECLARE datosLogin CURSOR FOR SELECT id_fabricante,nombre, CONCAT(LOWER(REPLACE(nombre, ' ', '')),'@gmail.com'), MD5('clave')  from fabricante;
  -- Condición de salida
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET fin=1;
    OPEN datosLogin;
    obtner_fila: LOOP
      FETCH datosLogin INTO cod_v, v_nombre, v_email, v_clave;
      IF fin = 1 THEN LEAVE obtner_fila; END IF;
      CALL insertarLogin(v_nombre, v_email, v_clave,4, @id_asignado);
      SELECT @id_asignado INTO v_id_login;
      UPDATE fabricante SET id_usuario = v_id_login WHERE fabricante.id_fabricante = cod_v;
    END LOOP obtner_fila;
    CLOSE datosLogin;
  END //
  DELIMITER ;

  CALL generarLoginFabricante();

-- info fabricantes
SELECT id_fabricante, usuario.id_usuario, usuario.foto, usuario.nombre, usuario.email,roles.nombre as roll, usuario.id_roll,pais.nombre as pais, pais.id_pais, pais.cod_pais, count(*) as farmacos from usuario left join roles on usuario.id_roll = roles.id_roll inner join fabricante on usuario.id_usuario = fabricante.id_usuario left join pais on fabricante.pais = pais.id_pais left join farmaco on fabricante.id_fabricante = farmaco.fabricante  group by fabricante.id_fabricante LIMIT 3;

    DROP PROCEDURE IF EXISTS infoFabricantes;
    DELIMITER //
    CREATE PROCEDURE infoFabricantes( IN id CHAR (3) ) BEGIN
    -- CAMPOS
    DECLARE id_fabricante INT(3);DECLARE id_usuario INT; DECLARE foto TEXT; DECLARE nombre VARCHAR(50);
    DECLARE email varchar(100); DECLARE roll varchar(30); DECLARE id_roll INT; DECLARE pais VARCHAR(30); DECLARE id_pais SMALLINT(3); DECLARE cod_pais VARCHAR(2); DECLARE farmacos INT;
    -- FIN 
    DECLARE fin INTEGER DEFAULT 0;
    -- cursores
    -- un solo registro
    DECLARE datosFabricante CURSOR FOR SELECT fabricante.id_fabricante, usuario.id_usuario, usuario.foto, usuario.nombre, usuario.email,roles.nombre as roll, usuario.id_roll,pais.nombre as pais, pais.id_pais, pais.cod_pais, count(farmaco.Fabricante) as farmacos from usuario left join roles on usuario.id_roll = roles.id_roll inner join fabricante on usuario.id_usuario = fabricante.id_usuario left join pais on fabricante.pais = pais.id_pais left join farmaco on fabricante.id_fabricante = farmaco.fabricante WHERE fabricante.id_fabricante = id group by fabricante.id_fabricante;
    -- totos los los registros
    DECLARE datosFabricantes CURSOR FOR SELECT fabricante.id_fabricante, usuario.id_usuario, usuario.foto, usuario.nombre, usuario.email,roles.nombre as roll, usuario.id_roll,pais.nombre as pais, pais.id_pais, pais.cod_pais, count(farmaco.Fabricante) as farmacos from usuario left join roles on usuario.id_roll = roles.id_roll inner join fabricante on usuario.id_usuario = fabricante.id_usuario left join pais on fabricante.pais = pais.id_pais left join farmaco on fabricante.id_fabricante = farmaco.fabricante group by fabricante.id_fabricante ORDER BY fabricante.id_fabricante DESC;

    -- Condición de salida
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET fin=1;
    -- recorriendo los resultados
    IF id > 0 THEN -- si el codigo es > 0, busca el reg. con ese codigo, habro su cursor
      OPEN datosFabricante;
        obtner_fila: LOOP 
          FETCH datosFabricante INTO id_fabricante,id_usuario, foto, nombre, email, roll, id_roll,pais,id_pais,cod_pais, farmacos;
          IF fin = 1 THEN LEAVE obtner_fila; END IF;
          SELECT id_fabricante,id_usuario, foto, nombre, email, roll, id_roll,pais,id_pais,cod_pais, farmacos;
        END LOOP obtner_fila;
      CLOSE datosFabricante;

    ELSE -- habro el cursor que devuelve la información de todos los registros
      OPEN datosFabricantes;
        obtner_fila: LOOP
            FETCH datosFabricantes INTO id_fabricante,id_usuario, foto, nombre, email, roll, id_roll,pais,id_pais,cod_pais, farmacos;
            IF fin = 1 THEN LEAVE obtner_fila; END IF;
            SELECT id_fabricante,id_usuario, foto, nombre, email, roll, id_roll,pais,id_pais,cod_pais,farmacos;
        END LOOP obtner_fila;
      CLOSE datosFabricantes;
    END IF;
    -- fin resultados
    END //
  DELIMITER ;
CALL infoFabricantes(1);

/*=======================================================================
            TRANSPORTISTA
=======================================================================*/
-- generar login
  DROP PROCEDURE IF EXISTS generarLoginTransportista;
  DELIMITER //
  CREATE PROCEDURE generarLoginTransportista () BEGIN
  -- Variables donde almacenar lo que nos traemos desde el SELECT
  DECLARE cod_v INT; DECLARE v_nombre VARCHAR(50); DECLARE v_clave VARCHAR(255); DECLARE v_email VARCHAR(50); DECLARE v_id_login INT;
  -- Variable para controlar el fin del bucle
    DECLARE fin INTEGER DEFAULT 0;
  -- El SELECT que vamos a ejecutar --> elimino los espacios en blanco de los nombres
    DECLARE datosLogin CURSOR FOR SELECT id_trans,nombre, CONCAT(LOWER(REPLACE(nombre, ' ', '')),'@gmail.com'), MD5('clave')  from transportista;
  -- Condición de salida
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET fin=1;
    OPEN datosLogin;
    obtner_fila: LOOP
      FETCH datosLogin INTO cod_v, v_nombre, v_email, v_clave;
      IF fin = 1 THEN LEAVE obtner_fila; END IF;
      CALL insertarLogin(v_nombre, v_email, v_clave,8, @id_asignado);
      SELECT @id_asignado INTO v_id_login;
      UPDATE transportista SET id_usuario = v_id_login WHERE transportista.id_trans = cod_v;
    END LOOP obtner_fila;
    CLOSE datosLogin;
  END //
  DELIMITER ;

  CALL generarLoginTransportista();
-- informacion de trsnsportistas
SELECT transportista.id_trans, usuario.id_usuario, usuario.foto, usuario.nombre, usuario.email,transportista.telefono, transportista.fax, transportista.provincia as id_provincia,roles.nombre as roll, usuario.id_roll,provincia.nombre as provincia, provincia.id_provincia, ccaa.nombre as ccaa, count(pedido.transporte) as pedidos from usuario 
left join roles on usuario.id_roll = roles.id_roll 
inner join transportista on usuario.id_usuario = transportista.id_usuario 
left join provincia on transportista.provincia = provincia.id_provincia
left join ccaa on provincia.id_ca = ccaa.id_ca 
left join pedido on transportista.id_trans = pedido.transporte 
group by transportista.id_trans 
ORDER BY transportista.fecha_registro DESC ;

/*=======================================
            ROLES 
=====================================*/
    DROP PROCEDURE IF EXISTS infoRoles;
    DELIMITER //
    CREATE PROCEDURE infoRoles( IN id INT ) BEGIN
    -- CAMPOS
    DECLARE id_roll INT; DECLARE nombre VARCHAR(30);DECLARE fecha_creacion DATE; DECLARE fecha_actualizacion DATE;
    -- FIN 
    DECLARE fin INTEGER DEFAULT 0;
    -- cursores
    -- un solo registro
    DECLARE datosRol CURSOR FOR  SELECT roles.id_roll, roles.nombre, roles.fecha_creacion, roles.ultima_actualizacion FROM roles WHERE roles.id_roll = id;
    -- totos los roles
    DECLARE datosRoles CURSOR FOR SELECT roles.id_roll, roles.nombre, roles.fecha_creacion, roles.ultima_actualizacion FROM roles ORDER BY roles.id_roll DESC;

    -- Condición de salida
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET fin=1;
    -- recorriendo los resultados
    IF id > 0 THEN -- si el codigo es > 0
      OPEN datosRol;
        obtner_fila: LOOP
          FETCH datosRol INTO id_roll, nombre, fecha_creacion, fecha_actualizacion;
          IF fin = 1 THEN LEAVE obtner_fila; END IF;
          SELECT id_roll, nombre, fecha_creacion, fecha_actualizacion;
        END LOOP obtner_fila;
      CLOSE datosRol;

    ELSE -- habro el cursor que devuelve la información de todos los paises
      OPEN datosRoles;
        obtner_fila: LOOP
            FETCH datosRoles INTO id_roll, nombre, fecha_creacion, fecha_actualizacion;
            IF fin = 1 THEN LEAVE obtner_fila; END IF;
            SELECT id_roll, nombre, fecha_creacion, fecha_actualizacion;
        END LOOP obtner_fila;
      CLOSE datosRoles;
    END IF;
    -- fin resultados
    END //
  DELIMITER ;
CALL infoRoles(1);


/*#######################################################################################
                    PAISES
#########################################################################################*/
  /*--------------------------------------------------------------------------------------------
  infoPaises(): devuelve la informacion de todos o un solo pais, segun el valor que reciba el parametro codigo
    --------------------------------------------------------------------------------*/
    DROP PROCEDURE IF EXISTS infoPaises;
    DELIMITER //
    CREATE PROCEDURE infoPaises( IN pais CHAR (4) ) BEGIN
    -- CAMPOS
    DECLARE id_pais INT(3); DECLARE nombre VARCHAR(30);DECLARE cod_pais VARCHAR(2); DECLARE abreviatura VARCHAR(3);
    -- FIN 
    DECLARE fin INTEGER DEFAULT 0;
    -- cursores
    -- un solo pais
    DECLARE datosPais CURSOR FOR  SELECT pais.id_pais,pais.cod_pais, pais.abreviatura,pais.nombre FROM pais WHERE pais.id_pais = pais;
    -- totos los paises
    DECLARE datosPaises CURSOR FOR  SELECT pais.id_pais,pais.cod_pais, pais.abreviatura,pais.nombre FROM pais ORDER BY pais.id_pais DESC;

    -- Condición de salida
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET fin=1;
    -- recorriendo los resultados
    IF pais > 0 THEN -- si el codigo es > 0, busca el pais con ese codigo, habro su cursor
      OPEN datosPais;
        obtner_fila: LOOP
          FETCH datosPais INTO id_pais, cod_pais, abreviatura, nombre;
          IF fin = 1 THEN LEAVE obtner_fila; END IF;
          SELECT id_pais, cod_pais, abreviatura, nombre;
        END LOOP obtner_fila;
      CLOSE datosPais;

    ELSE -- habro el cursor que devuelve la información de todos los paises
      OPEN datosPaises;
        obtner_fila: LOOP
            FETCH datosPaises INTO id_pais, cod_pais, abreviatura, nombre;
            IF fin = 1 THEN LEAVE obtner_fila; END IF;
            SELECT id_pais, cod_pais, abreviatura, nombre;
        END LOOP obtner_fila;
      CLOSE datosPaises;
    END IF;
    -- fin resultados
    END //
  DELIMITER ;
CALL infoPaises(1);

/*----------------------------------
  actualizar informacion de un pais 
  devuelve:
    1: si se ha actualizado el pais
    0: si no se ha actualizado
   -1: si ya existe un pais con los mismos datos    
  -- SET mensaje = '["exito"= true, "mensaje": Exite el pais]';
------------------------------------*/
DROP PROCEDURE IF EXISTS actualizarInfoPais;
DELIMITER //
CREATE PROCEDURE actualizarInfoPais(IN pais CHAR(3), IN codigo VARCHAR(2), IN abv VARCHAR(3), IN nombre VARCHAR (30), OUT mensaje SMALLINT(1) ) BEGIN
  DECLARE existe INT(2);

 -- verificar si es necesario actualizar
  SELECT COUNT(*) INTO existe FROM pais WHERE cod_pais = codigo AND abreviatura = abv AND pais.nombre = nombre;
  IF existe < 1 THEN -- no existe
    UPDATE pais SET pais.nombre = nombre, cod_pais = codigo, abreviatura = abv WHERE id_pais = pais;
    IF ROW_COUNT() > 0 THEN
      SET mensaje = 1; -- todo ok
    ELSE
      SET mensaje = 0; -- no se ha actualizado
    END IF;
  ELSE 
      SET mensaje = -1; -- ya existe
  END IF;

END //
DELIMITER ;
CALL actualizarInfoPais(1,'FR', 'FRA', 'FRANCIA', @mensaje);

/* 
  Eliminar un pais de la base de datos
*/
DROP PROCEDURE IF EXISTS eliminarPais;
DELIMITER //
CREATE PROCEDURE eliminarPais(IN codigo VARCHAR (3), OUT mensaje INT(1)) 
BEGIN
  -- Filas afectadas
  -- DECLARE filasAfectadas INT;
  DELETE FROM pais WHERE id_pais = codigo;
  -- SELECT ROW_COUNT() INTO filasAfectadas;

  IF ROW_COUNT() > 0 THEN
    SET mensaje = 1;
  ELSE
    SET mensaje = 0;
  END IF;
END //
DELIMITER ;

CALL eliminarPais(959, @mensaje);
SELECT @mensaje;


/*
  insertar paises
*/

DROP PROCEDURE IF EXISTS insertarNuevoPais;
DELIMITER //
CREATE PROCEDURE insertarNuevoPais(IN cod_pais VARCHAR(2), IN abreviatura VARCHAR(3), IN nombre VARCHAR(30), OUT id_asignado INT )
BEGIN 
  DECLARE id_asignar SMALLINT(3); DECLARE id SMALLINT(3); DECLARE existe SMALLINT(3);

  -- asegurarse de que no exista ningun pais con los mismos datos (duplicado)
  SELECT COUNT(*) INTO existe FROM pais WHERE pais.cod_pais = cod_pais AND pais.abreviatura = abreviatura AND pais.nombre = nombre; 

  IF existe < 1 THEN -- NO EXISTE PAIS CON LOS MISMO DATOS
    SELECT MAX(id_pais) + 1 INTO id FROM pais;

    IF (id <= 999) THEN
      SET id_asignar = id;
    ELSE
        SELECT MIN(id_pais) + 1 INTO id FROM pais WHERE (id_pais + 1) NOT IN (SELECT id_pais FROM pais) AND ( id_pais + 1 BETWEEN 1 AND 999);
        SET id_asignar = id;
    END IF;
      SET id_asignado = id;

    INSERT INTO pais VALUES (id_asignar, cod_pais, abreviatura, nombre);
  ELSE -- YA EXISTE EL PAIS 
    SET id_asignado = 0; -- si id_asignado = 0, significara que ya existe el pais
  END IF;

END //
DELIMITER ;
CALL insertarNuevoPais('PP', 'TPT', 'TESTEO PAIS PRUEBA 1',@id_asignado);
CALL insertarNuevoPais('PP', 'TPT', 'TESTEO PAIS PRUEBA 2',@id_asignado);
CALL insertarNuevoPais('PP', 'TPT', 'TESTEO PAIS PRUEBA 3',@id_asignado);
CALL insertarNuevoPais('PP', 'TPT', 'TESTEO PAIS PRUEBA 4',@id_asignado);
CALL insertarNuevoPais('PP', 'TPT', 'TESTEO PAIS PRUEBA 5',@id_asignado);
CALL insertarNuevoPais('PP', 'TPT', 'TESTEO PAIS PRUEBA 6',@id_asignado);
CALL insertarNuevoPais('PP', 'TPT', 'TESTEO PAIS PRUEBA 7',@id_asignado);
SELECT @id_asignado;



/*#######################################################################################
                    COMUNIDADES AUTONOMAS
#########################################################################################*/
  /*--------------------------------------------------------------------------------------------
    devuleve la informacion de una comunidad autonoma o de todas(si id = 0 )
    --------------------------------------------------------------------------------*/
    DROP PROCEDURE IF EXISTS infoComunidadesAutonomas;
    DELIMITER //
    CREATE PROCEDURE infoComunidadesAutonomas( IN id CHAR (2) ) BEGIN
    -- CAMPOS
    DECLARE id_ca INT(2); DECLARE nombre VARCHAR(20);
    -- FIN 
    DECLARE fin INTEGER DEFAULT 0;
    -- cursores
    -- una sola comunidad
    DECLARE datosCCAA CURSOR FOR  SELECT ccaa.id_ca, ccaa.nombre FROM ccaa WHERE ccaa.id_ca = id;
    -- totos los paises
    DECLARE datosCCAAs CURSOR FOR SELECT ccaa.id_ca, ccaa.nombre FROM ccaa ORDER BY ccaa.id_ca DESC;

    -- Condición de salida
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET fin=1;
    -- recorriendo los resultados
    IF id > 0 THEN -- si el id es > 0, busca la ccaa con ese id, habro su cursor
      OPEN datosCCAA;
        obtner_fila: LOOP
          FETCH datosCCAA INTO id_ca, nombre;
          IF fin = 1 THEN LEAVE obtner_fila; END IF;
          SELECT id_ca, nombre;
        END LOOP obtner_fila;
      CLOSE datosCCAA;

    ELSE -- habro el cursor que devuelve la información de todas las comunidades autonomas
      OPEN datosCCAAs;
        obtner_fila: LOOP
            FETCH datosCCAAs INTO id_ca, nombre;
            IF fin = 1 THEN LEAVE obtner_fila; END IF;
            SELECT id_ca, nombre;
        END LOOP obtner_fila;
      CLOSE datosCCAAs;
    END IF;
    -- fin resultados
    END //
  DELIMITER ;
CALL infoComunidadesAutonomas(1);

/*----------------------------------
  actualizar informacion de una comunidad autonoma 
  devuelve:
    1: si se ha actualizado el registro
    0: si no se ha actualizado
   -1: si ya existe un registro con los mismos datos    
------------------------------------*/
DROP PROCEDURE IF EXISTS actualizarCCAA;
DELIMITER //
CREATE PROCEDURE actualizarCCAA(IN id CHAR(3), IN nombre VARCHAR (20), OUT mensaje SMALLINT(1) ) BEGIN
  DECLARE existe INT(2); -- por si existe el registro

 -- verificar si es necesario actualizar
  SELECT COUNT(*) INTO existe FROM ccaa WHERE id_ca = id AND ccaa.nombre = nombre;
  IF existe < 1 THEN -- no existe
    UPDATE ccaa SET ccaa.nombre = nombre WHERE id_ca = id;
    IF ROW_COUNT() > 0 THEN
      SET mensaje = 1; -- todo ok
    ELSE
      SET mensaje = 0; -- no se ha actualizado
    END IF;
  ELSE 
      SET mensaje = -1; -- ya existe
  END IF;

END //
DELIMITER ;
CALL actualizarCCAA(18,'CEUTAS', @mensaje);
-- CALL infoComunidadesAutonomas(18);

/* 
  Eliminar una ccaa
*/

DROP PROCEDURE IF EXISTS eliminarCCAA;
DELIMITER //
CREATE PROCEDURE eliminarCCAA(IN id VARCHAR (2), OUT mensaje INT(1)) 
BEGIN
  DELETE FROM ccaa WHERE id_ca = id;
  IF ROW_COUNT() > 0 THEN
    SET mensaje = 1; --  se ha eliminado
  ELSE
    SET mensaje = 0; -- no se ha eliminado
  END IF;
END //
DELIMITER ;

CALL eliminarCCAA(95, @mensaje);
SELECT @mensaje;


/*
  insertar ccaa
*/

DROP PROCEDURE IF EXISTS insertarCCAA;
DELIMITER //
CREATE PROCEDURE insertarCCAA(IN nombre VARCHAR(20), OUT id_asignado INT )
BEGIN 
  DECLARE id_asignar SMALLINT(3); DECLARE id SMALLINT(3); DECLARE existe SMALLINT(3);

  -- asegurarse de que no exista ningun registro con los mismos datos (duplicado)
  SELECT COUNT(*) INTO existe FROM ccaa WHERE ccaa.nombre = nombre; 

  IF existe < 1 THEN -- NO EXISTE 
    CALL generarIdCCAA(@id);
    SELECT @id INTO id;
    IF (id > 0 ) THEN
      SET id_asignado = id;
      INSERT INTO ccaa VALUES (id, nombre);
    ELSE
      SET id_asignado = -1;
    END IF;
  ELSE -- YA EXISTE 
    SET id_asignado = 0; -- si id_asignado = 0, significara que ya existe
  END IF;

END //
DELIMITER ;

CALL insertarCCAA('TESTEO 1',@id_asignado);
CALL insertarCCAA('TESTEO 2',@id_asignado);

/* --- Generador de ID para las ccaa ---*/
DROP PROCEDURE IF EXISTS generarIdCCAA;
DELIMITER //
CREATE PROCEDURE generarIdCCAA( OUT id INT) BEGIN
  DECLARE id_asignar SMALLINT(3);
  SELECT MAX(id_ca) + 1 INTO id_asignar FROM ccaa;
  IF (id_asignar <= 99) THEN 
    SET id = id_asignar;
  ELSE
    SELECT MIN(id_ca) + 1 INTO id_asignar FROM ccaa WHERE (id_ca + 1) NOT IN (SELECT id_ca FROM ccaa) AND ( (id_ca + 1) BETWEEN 1 AND 99);
    IF (id_asignar > 0) THEN SET id = id_asignar; ELSE SET id = 0; END IF;
  END IF;
END //
DELIMITER ;




/*#######################################################################################
                    PROVINCIAS
#########################################################################################*/
  /*--------------------------------------------------------------------------------------------
    devuleve la informacion de una provincia o de todas(si id = 0 )
    --------------------------------------------------------------------------------*/
    DROP PROCEDURE IF EXISTS infoProvincias;
    DELIMITER //
    CREATE PROCEDURE infoProvincias( IN id CHAR (2) ) BEGIN
    -- CAMPOS
    DECLARE id_provincia INT(2); DECLARE nombre VARCHAR(16); DECLARE id_ca SMALLINT(2); DECLARE nombre_ccaa VARCHAR(20);
    -- FIN 
    DECLARE fin INTEGER DEFAULT 0;
    -- cursores
    -- una sola provincia
    DECLARE datosProvincia CURSOR FOR  SELECT provincia.id_provincia, provincia.nombre, provincia.id_ca, ccaa.nombre FROM provincia LEFT JOIN ccaa ON provincia.id_ca = ccaa.id_ca WHERE provincia.id_provincia = id;
    -- totos los paises
    DECLARE datosProvincias CURSOR FOR SELECT provincia.id_provincia, provincia.nombre, provincia.id_ca, ccaa.nombre FROM provincia LEFT JOIN ccaa ON provincia.id_ca = ccaa.id_ca ORDER BY provincia.id_provincia DESC;

    -- Condición de salida
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET fin=1;
    -- recorriendo los resultados
    IF id > 0 THEN -- si el id es > 0, busca la ccaa con ese id, habro su cursor
      OPEN datosProvincia;
        obtner_fila: LOOP
          FETCH datosProvincia INTO id_provincia, nombre, id_ca, nombre_ccaa;
          IF fin = 1 THEN LEAVE obtner_fila; END IF;
          SELECT id_provincia, nombre, id_ca, nombre_ccaa;
        END LOOP obtner_fila;
      CLOSE datosProvincia;

    ELSE -- habro el cursor que devuelve la información de todas las comunidades autonomas
      OPEN datosProvincias;
        obtner_fila: LOOP
            FETCH datosProvincias INTO id_provincia, nombre,id_ca, nombre_ccaa;
            IF fin = 1 THEN LEAVE obtner_fila; END IF;
            SELECT id_provincia, nombre, id_ca, nombre_ccaa;
        END LOOP obtner_fila;
      CLOSE datosProvincias;
    END IF;
    -- fin resultados
    END //
  DELIMITER ;
CALL infoProvincias(1);

/*----------------------------------
  actualizar informacion de una provincia 
  devuelve:
    1: si se ha actualizado el registro
    0: si no se ha actualizado
   -1: si ya existe un registro con los mismos datos    
------------------------------------*/
DROP PROCEDURE IF EXISTS actualizarProvincia;
DELIMITER //
CREATE PROCEDURE actualizarProvincia(IN id CHAR(2), IN nombre VARCHAR (16), IN id_ca INT(2), OUT mensaje SMALLINT(1) ) BEGIN
  DECLARE existe INT(2); -- por si existe el registro

 -- verificar si es necesario actualizar
  SELECT COUNT(*) INTO existe FROM provincia WHERE id_provincia = id AND provincia.nombre = nombre AND provincia.id_ca = id_ca;
  IF existe < 1 THEN -- no existe
      -- el id_ca puede estar vacio
    IF id_ca <= 0 THEN
      SET id_ca = NULL;
    END IF;
    UPDATE provincia SET provincia.nombre = nombre, provincia.id_ca = id_ca WHERE id_provincia = id;
    IF ROW_COUNT() > 0 THEN
      SET mensaje = 1; -- todo ok
    ELSE
      SET mensaje = 0; -- no se ha actualizado
    END IF;
  ELSE 
      SET mensaje = -1; -- ya existe
  END IF;

END //
DELIMITER ;
-- ALAVA
CALL actualizarProvincia(1,'ALAVA',15, @mensaje);
CALL infoProvincias(1);

/* 
  Eliminar una provincia
*/
/* 
  Eliminar una provincia
*/
DROP PROCEDURE IF EXISTS eliminarProvincia;
DELIMITER //
CREATE PROCEDURE eliminarProvincia(IN id VARCHAR (2), OUT mensaje INT(1)) 
BEGIN
  DELETE FROM provincia WHERE id_provincia = id;
  IF ROW_COUNT() > 0 THEN
    SET mensaje = 1; --  se ha eliminado
  ELSE
    SET mensaje = 0; -- no se ha eliminado
  END IF;
END //
DELIMITER ;

CALL eliminarProvincia(99, @mensaje);
SELECT @mensaje;


/*
  insertar provincia
*/

DROP PROCEDURE IF EXISTS insertarProvincia;
DELIMITER //
CREATE PROCEDURE insertarProvincia(IN nombre VARCHAR(16), id_ca VARCHAR(2), OUT id_asignado INT )
BEGIN 
  DECLARE id_ccaa SMALLINT(2); DECLARE id SMALLINT(3); DECLARE existe SMALLINT(3);

  -- asegurarse de que no exista ningun registro con los mismos datos (duplicado)
  SELECT COUNT(*) INTO existe FROM provincia WHERE provincia.nombre = nombre AND provincia.id_ca = id_ca;
  -- el id_ca puede estar vacio
  IF id_ca != 0 THEN
    SET id_ccaa = id_ca;
  ELSE
    SET id_ccaa = NULL;
  END IF;

  IF existe < 1 THEN -- NO EXISTE 
    CALL generarIdProvincia(@id);
    SELECT @id INTO id;
    IF (id > 0 ) THEN -- hay id disponible y asignable
      SET id_asignado = id;
      INSERT INTO provincia VALUES (id, nombre, id_ccaa);
    ELSE
      SET id_asignado = -1; -- no hay id, el genrador de id devuelve 0 = no hay disponibles
    END IF;
  ELSE -- YA EXISTE 
    SET id_asignado = 0; -- si id_asignado = 0, significara que ya existe
  END IF;

END //
DELIMITER ;

CALL insertarProvincia('TESTEO 3',0,@id_asignado);
CALL insertarProvincia('TESTEO 2',@id_asignado);

/* --- Generador de ID para las provincias ---*/
DROP PROCEDURE IF EXISTS generarIdProvincia;
DELIMITER //
CREATE PROCEDURE generarIdProvincia( OUT id INT) BEGIN
  DECLARE id_asignar SMALLINT(3);
  SELECT MAX(id_provincia) + 1 INTO id_asignar FROM provincia;
  IF (id_asignar <= 99) THEN 
    SET id = id_asignar;
  ELSE
    SELECT MIN(id_provincia) + 1 INTO id_asignar FROM provincia WHERE (id_provincia + 1) NOT IN (SELECT id_provincia FROM provincia) AND ( (id_provincia + 1) BETWEEN 1 AND 99);
    IF (id_asignar > 0) THEN SET id = id_asignar; ELSE SET id = 0; END IF;
  END IF;
END //
DELIMITER ;









