#Modulos agregados:

cookies-js: Para utilizar cookies: almacenar y manejar datos de los juegos y token.

jwt-decode: Para decodificar tokens.

#Endpoints agregados/editados:

Soporte: 
Tuve que agregar el endpoint de soporte, porque a la hora de agregar un nuevo videojuego se pide que se agregue las plataformas 
en las cuales el juego es jugable.
Para eso, necesito crear el soporte en esa consola seleccionada.
Entonces fue creada la clase Modelo y el Controlador de Soporte, donde se agrego la funcion de agregar un nuevo soporte.
Donde se envia la id del videojuego y la id de la plataforma.

Calificaciones:
Agregue dos endpoints nuevos en la seccion de calificacion:

Obtener Calificaciones: se obtiene todas las calificaciones de un usuario en especifico. Es utilizado en la pagina de Usuario, donde se muestra
informacion del usuario, su nombre y sus calificaciones.

ObtenerCalificacion: obtiene la calificacion de un usuario en especifico de un juego en especifico. Utilizado en la pagina de Juegos, donde
se determina que si tiene calificacion, pueda editarla o borrarla, y en caso que no la tenga pueda crear una nueva.

Juegos: 
ObtenerJuegos: esta funcion obtiene los juegos completos con todos sus detalles. Es utilizado en la pagina de Usuario donde se muestran
todas las calificaciones del usuario en todos los juegos. Porque al utilizar /juegos obtenemos los juegos de a 5 y preferi utilizar un nuevo endpoint
/juegosCompletos asi obtengo todos los juegos en un solo array

Tambien se actualizo el endpoint /juegos para que devuelva la cantidad total de juegos para facilitar el manejo de datos en el front,
 como la paginacion. Tambien ahora devuelve todas las plataformas donde el juego esta disponible.

En el endpoint de POST de /juego donde se crea un nuevo juego, almaceno el id del juego creado, que lo solicito desde lastInsertId();
esto lo almaceno para despues hacer el insert en soporte, donde agrego el soporte de ese juego en todas las plataformas seleccionadas.

:solicitar en la ruta /juegos/{id} ahora devuelve las calificaciones del juego y todos sus soportes. Lo que se modifico tambien, fueron la forma
en la que se traen los datos de las calificaciones: ahora tambien trae el nombre de los usuarios, esto fue usado para la pagina JuegoDetalles

Plataformas:
Obtener Plataformas: agregue un endpoint en plataformas que me traiga todas las plataformas con su informacion. Es utilizado en JuegoNuevo
donde le damos de alta a un videojuego, es utilizado para obtener todas las plataformas disponibles y seleccionar en cuales esta disponible tal juego.
Tambien es utilizado de la misma manera en la pagina principal, para obtener todas las plataformas disponibles y poder filtrar los juegos
por plataforma.


# Getting Started with Create React App

This project was bootstrapped with [Create React App](https://github.com/facebook/create-react-app).

## Available Scripts

In the project directory, you can run:

### `npm start`

Runs the app in the development mode.\
Open [http://localhost:3000](http://localhost:3000) to view it in your browser.

The page will reload when you make changes.\
You may also see any lint errors in the console.

### `npm test`

Launches the test runner in the interactive watch mode.\
See the section about [running tests](https://facebook.github.io/create-react-app/docs/running-tests) for more information.

### `npm run build`

Builds the app for production to the `build` folder.\
It correctly bundles React in production mode and optimizes the build for the best performance.

The build is minified and the filenames include the hashes.\
Your app is ready to be deployed!

See the section about [deployment](https://facebook.github.io/create-react-app/docs/deployment) for more information.

### `npm run eject`

**Note: this is a one-way operation. Once you `eject`, you can't go back!**

If you aren't satisfied with the build tool and configuration choices, you can `eject` at any time. This command will remove the single build dependency from your project.

Instead, it will copy all the configuration files and the transitive dependencies (webpack, Babel, ESLint, etc) right into your project so you have full control over them. All of the commands except `eject` will still work, but they will point to the copied scripts so you can tweak them. At this point you're on your own.

You don't have to ever use `eject`. The curated feature set is suitable for small and middle deployments, and you shouldn't feel obligated to use this feature. However we understand that this tool wouldn't be useful if you couldn't customize it when you are ready for it.

## Learn More

You can learn more in the [Create React App documentation](https://facebook.github.io/create-react-app/docs/getting-started).

To learn React, check out the [React documentation](https://reactjs.org/).

### Code Splitting

This section has moved here: [https://facebook.github.io/create-react-app/docs/code-splitting](https://facebook.github.io/create-react-app/docs/code-splitting)

### Analyzing the Bundle Size

This section has moved here: [https://facebook.github.io/create-react-app/docs/analyzing-the-bundle-size](https://facebook.github.io/create-react-app/docs/analyzing-the-bundle-size)

### Making a Progressive Web App

This section has moved here: [https://facebook.github.io/create-react-app/docs/making-a-progressive-web-app](https://facebook.github.io/create-react-app/docs/making-a-progressive-web-app)

### Advanced Configuration

This section has moved here: [https://facebook.github.io/create-react-app/docs/advanced-configuration](https://facebook.github.io/create-react-app/docs/advanced-configuration)

### Deployment

This section has moved here: [https://facebook.github.io/create-react-app/docs/deployment](https://facebook.github.io/create-react-app/docs/deployment)

### `npm run build` fails to minify

This section has moved here: [https://facebook.github.io/create-react-app/docs/troubleshooting#npm-run-build-fails-to-minify](https://facebook.github.io/create-react-app/docs/troubleshooting#npm-run-build-fails-to-minify)
