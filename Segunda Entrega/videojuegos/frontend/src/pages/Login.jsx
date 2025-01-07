import React, { useRef, useState } from "react";
import Cookies from "cookies-js";
import styles from '../assets/styles/Login.module.css'
import { useAuth } from '../config/auth';
import { realizarSolicitud } from "../config/data";
import { BASE_URL } from '../config/data';

const patronAlfanumerico = /^[A-Za-z0-9ñ]{6,20}$/;
const patronContraseña = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[!#$%&'*-_"+@?¡{}.,<>¿'])[A-Za-z\d!#$%&'*-_"+@?¡{}.,<>¿'ñ]{8,16}$/;

export default function Login() {
  const refUsuario = useRef(null);
  const refClave = useRef(null);
  const { login } = useAuth();
  
  const [errorMessage, setErrorMessage] = useState("");
  const [errorMessagePass, setErrorMessagePass] = useState("");
  const [successMessage, setSuccessMessage] = useState("");
  const [Logeando, setLogeando] = useState(false);

  const handleLogin = async () => {
    const data = {
      nombre_usuario: refUsuario.current.value,
      clave: refClave.current.value,
    };
    
    if(!patronAlfanumerico.test(refUsuario.current.value)){
      setErrorMessage("El nombre de usuario debe tener entre 6 y 20 caracteres alfanumericos.")
      return;
    }
  
    if(!patronContraseña.test(refClave.current.value)){
      console.log(refClave.current.value)
      setErrorMessagePass("La contraseña debe tener entre 8 y 16 caracteres, incluyendo numeros y caracteres especiales.")
      return;
    }
    
    setLogeando(true);

    const respuestaJson = await realizarSolicitud(BASE_URL+"login","POST",data);
    
    if (respuestaJson.Codigo === 200) {
      const { token, vencimiento, id, a, nombre_usuario} = respuestaJson.Data;
      const expirationDate = new Date(vencimiento);
      setErrorMessage("");
      setErrorMessagePass("");
      setSuccessMessage("Inicio de sesión exitoso");
      Cookies.set('token', token,{expires: expirationDate} ) ;
      Cookies.set('id', id ,{expires: expirationDate}) ;
      Cookies.set('a', a ,{expires: expirationDate}) ;
      Cookies.set('nombre_usuario', nombre_usuario ,{expires: expirationDate});
      setTimeout( () => {window.location.assign("/juegos")}, 2000);
      setTimeout( () => {login(token)},2500)
    } else {
      setLogeando(false);
      setSuccessMessage("");
      setErrorMessage(respuestaJson.Mensaje);
    }
  };

  return (
    <div id={styles.login}>
      <div id={styles.loginContainer}>
        <div id={styles.cardBody}>
          <h2>Login</h2>
          {errorMessage && <p className={styles.error}>{errorMessage}</p>}

          <input
            className={styles.inputLogin}
            type="text"
            placeholder="Nombre de usuario"
            maxLength={20}
            minLength={6}
            ref={refUsuario}
          />
          
          
          <input
            className={styles.inputLogin}
            type="password"
            placeholder="Contraseña"
            ref={refClave}
            maxLength={16}
            minLength={8}
          />{Logeando ? <button id={styles.botonLogin} disabled>
          Cargando...
        </button> : <button id={styles.botonLogin} onClick={handleLogin}>
            Acceder
          </button>}
          
          {errorMessagePass && <p className={styles.error}>{errorMessagePass}</p>}
          

          {successMessage && <p id={styles.exito}>{successMessage}</p>}
          
        </div>
      </div>
    </div>
  );
}
