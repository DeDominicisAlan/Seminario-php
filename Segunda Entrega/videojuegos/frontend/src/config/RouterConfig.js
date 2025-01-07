import { BrowserRouter, Route, Routes} from 'react-router-dom';
import { Paths } from './paths';

/* Page component */

import Login from "../pages/Login";
import Not_Found from '../pages/NotFound';
import JuegoPage from '../pages/juego/JuegoPage'
import Usuario from '../pages/Usuario'
import Register from '../pages/registro/RegistroPage'
import Juego from '../pages/juego/JuegoDetalles'
import Calificacion from '../pages/Calificacion/Calificacion'
import JuegoNuevo from '../pages/juego/JuegoNuevo'

export const RouterConfig = () =>{ 
  return (
    <BrowserRouter>
      <Routes>
        <Route path={Paths.LOGIN} element={<Login />} />;
        <Route path={Paths.JUEGOS} element={<JuegoPage />} />;
        <Route path={Paths.JUEGO} element={<Juego />} />;
        <Route path={Paths.DASHBOARD} element={<JuegoPage />} />;
        <Route path={Paths.USUARIO} element={<Usuario />}/>
        <Route path={Paths.REGISTRO} element={<Register />}/>
        <Route path={Paths.CALIFICACION} element={<Calificacion />}/>
        <Route path={Paths.JUEGONUEVO} element={<JuegoNuevo />} />;
        {/* NOT FOUND ROUTE */}
        <Route path={Paths.NOT_FOUND} element={<Not_Found />} />;
      </Routes>
    </BrowserRouter>
  )
}