# Guía de Solución de Problemas - Panel de Acceso

## Problema: "No responde el servidor"

### Soluciones Implementadas:

#### 1. ✅ Error SQL Corregido
- **Problema**: La consulta SQL tenía 5 placeholders pero solo 4 columnas
- **Solución**: Corregido en `PHP/registro.php` línea 16-17

#### 2. ✅ Campos Duplicados Corregidos
- **Problema**: Dos campos con el mismo ID `register-name`
- **Solución**: Cambiado el primer campo a `register-username` en `panel_de_acceso.html`

#### 3. ✅ Mejor Manejo de Errores
- **Agregado**: Validación de campos y mensajes de error claros
- **Agregado**: Hash de contraseñas con `password_hash()`
- **Agregado**: Detección de usuarios duplicados

---

## Pasos para Verificar que Todo Funcione:

### Paso 1: Verificar que MAMP esté corriendo
1. Abre MAMP
2. Asegúrate que Apache y MySQL estén en verde (iniciados)
3. El puerto de Apache debe ser 80 o 8888
4. El puerto de MySQL debe ser 3306

### Paso 2: Verificar la Base de Datos
1. Abre phpMyAdmin: `http://localhost/phpMyAdmin` o `http://localhost:8888/phpMyAdmin`
2. Verifica que existe la base de datos `lukes`
3. Si no existe, ejecuta el script: `PHP/database/script.sql`

### Paso 3: Probar la Conexión
1. Abre en tu navegador: `http://localhost/Proyecto/PHP/test_connection.php`
2. Deberías ver:
   - ✅ Connected successfully to database: lukes
   - ✅ Table 'users' exists
   - Lista de columnas de la tabla

### Paso 4: Probar el Registro
1. Abre: `http://localhost/Proyecto/panel_de_acceso.html`
2. Haz clic en "Abrir Panel de Acceso"
3. Ve a la pestaña "Registrarse"
4. Llena todos los campos:
   - Usuario
   - Nombre completo
   - Correo electrónico
   - Contraseña (mínimo 6 caracteres)
   - Confirmar contraseña
5. Haz clic en "Registrarse"

---

## Errores Comunes y Soluciones:

### Error: "Connection failed: Access denied"
**Causa**: Credenciales incorrectas de MySQL
**Solución**: 
- Verifica en `PHP/includes/config.php`:
  ```php
  $DB_USERNAME = "root";
  $DB_PASSWORD = "root"; // o "" si no tiene contraseña
  ```

### Error: "Unknown database 'lukes'"
**Causa**: La base de datos no existe
**Solución**: 
1. Abre phpMyAdmin
2. Crea una nueva base de datos llamada `lukes`
3. Ejecuta el script SQL: `PHP/database/script.sql`

### Error: "Table 'lukes.users' doesn't exist"
**Causa**: La tabla no fue creada
**Solución**: 
1. Abre phpMyAdmin
2. Selecciona la base de datos `lukes`
3. Ve a la pestaña SQL
4. Copia y pega el contenido de `PHP/database/script.sql`
5. Ejecuta

### Error: "El email o usuario ya está registrado"
**Causa**: Ya existe un usuario con ese email o nombre de usuario
**Solución**: 
- Usa un email diferente
- O elimina el registro anterior desde phpMyAdmin

### Error: "CORS" o "No se puede conectar"
**Causa**: El servidor no está corriendo o la ruta es incorrecta
**Solución**: 
- Verifica que MAMP esté corriendo
- Asegúrate de acceder vía `http://localhost/Proyecto/` no desde `file://`

---

## Verificación Final:

### Checklist:
- [ ] MAMP está corriendo (Apache y MySQL en verde)
- [ ] Base de datos `lukes` existe
- [ ] Tabla `users` existe con las columnas correctas
- [ ] `test_connection.php` muestra conexión exitosa
- [ ] Puedes acceder a `panel_de_acceso.html` vía localhost
- [ ] El formulario de registro se envía sin errores

---

## Archivos Modificados:
1. `PHP/registro.php` - Corregido SQL y agregado mejor manejo de errores
2. `panel_de_acceso.html` - Corregido ID duplicado del campo username
3. `PHP/test_connection.php` - Nuevo archivo para probar conexión

## Contacto:
Si el problema persiste, verifica los logs de error de PHP en:
- MAMP: `Applications/MAMP/logs/php_error.log` (Mac)
- MAMP: `C:\MAMP\logs\php_error.log` (Windows)
