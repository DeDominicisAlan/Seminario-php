import styles from "../assets/styles/Navbar.module.css";
import Cookies from "cookies-js";
import { useAuth } from '../config/auth';
import { HeaderComponent } from '../components/HeaderComponent';
import { useState } from "react";


export function NavbarComponent() {
  const { isLoggedIn, logout } = useAuth();
  const esAdmin = Cookies.get('a')
  const usuario = Cookies.get('nombre_usuario')
  const [menu, setMenu] = useState(false);

  const handleLogout = () => {
    logout();
    window.location.assign("/login")
   };
   
   const toggleMenu = () =>{
    setMenu(!menu);
   }

  return (
    <nav id={styles.navbar}>
      <HeaderComponent />
      <button id={styles.menuButton} onClick={toggleMenu}>
        ☰ 
      </button>
      <ul id={styles.navbarLista} className={menu ? styles.show : ""}> {/* Si menu = true, le asignamos la clase para el responsive */}
        <li>
          <a href="/juegos">Inicio</a>
        </li>
        {isLoggedIn ? (
          <>
            <li>
              <a href="/usuario">{usuario}</a>
            </li>
            {esAdmin === "1" ? <li><a href="/juego/nuevo">Añadir juego</a></li> : <div></div>}
            <li>
              <p onClick={handleLogout} id={styles.cerrarSesion}>Cerrar sesion</p>
            </li>
            
          </>
        ) : (
          <>
            <li>
              <a href="/login">Login</a>
            </li>
            <li>
              <a href="/register">Registro</a>
            </li>
          </>
        )}
        
      </ul>
    </nav>
  );
}
