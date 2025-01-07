import React, { useRef, useState } from "react";
import styles from '../../assets/styles/Register.module.css'
import { realizarSolicitud, BASE_URL } from "../../config/data";

const patronAlfanumerico = /^[A-Za-z0-9ñ]{6,20}$/;
const patronContraseña = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[!#$%&'*-_"+@?¡{}.,<>¿'])[A-Za-z\d!#$%&'*-_"+@?¡{}.,<>¿'ñ]{8,16}$/;

export default function Register() {
  const refUsuario = useRef(null);
  const refClave = useRef(null);
  
  const [errorMessage, setErrorMessage] = useState("");
  const [errorMessagePass, setErrorMessagePass] = useState("");
  const [successMessage, setSuccessMessage] = useState("");
  const [isSubmitting, setIsSubmitting] = useState(false);

  const handleRegister = async () => {
    setErrorMessage("");
    setErrorMessagePass("");
    if(!patronAlfanumerico.test(refUsuario.current.value)){
      setErrorMessage("El nombre de usuario debe tener entre 6 y 20 caracteres alfanumericos.")
      return;
    }
  
    if(!patronContraseña.test(refClave.current.value)){
      console.log(refClave.current.value)
      setErrorMessagePass("La contraseña debe tener entre 8 y 16 caracteres, incluyendo numeros y caracteres especiales.")
      return;
    }
  
    const data = {
      nombre_usuario: refUsuario.current.value,
      clave: refClave.current.value,
    };
    
    setIsSubmitting(true)
    const respuestaJson = await realizarSolicitud(`${BASE_URL}register`,"POST" ,data);
    if (respuestaJson.Codigo === 200) {
      setErrorMessage("");
      setSuccessMessage("Registro exitoso");
      setTimeout( () => {window.location.assign("/login")}, 2000);
    } else {
      setIsSubmitting(false)
      setSuccessMessage("");
      setErrorMessage(respuestaJson.Mensaje);
    }
  };

  return (
    <div id={styles.register}>
      <div id={styles.registerContainer}>
        <div id={styles.cardBody}>
          <h2>Registro</h2>
          {errorMessage && <p id={styles.error}>{errorMessage}</p>}
          <input
            className={styles.inputReg}
            type="text"
            placeholder="Nombre de usuario"
            maxLength={20}
            minLength={6}
            ref={refUsuario}
          />
          {errorMessagePass && <p id={styles.error}>{errorMessagePass}</p>}
          <input
            className={styles.inputReg}
            type="password"
            placeholder="Contraseña"
            maxLength={16}
            minLength={8}
            ref={refClave}
          />{isSubmitting 
          ?  <button id={styles.botonReg} disabled> Cargando...</button> 
          :  <button id={styles.botonReg} onClick={handleRegister}>Registrarse</button>}
          

          {successMessage && <p id={styles.exito}>{successMessage}</p>}
          
        </div>
      </div>
    </div>
  );
}
