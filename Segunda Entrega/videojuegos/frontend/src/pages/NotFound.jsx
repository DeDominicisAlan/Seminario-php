import React from 'react';
import style from '../assets/styles/NotFound.module.css'

const Not_Found = () =>{
  return (<div id={style.contenedorNotFound}>
   <h1 id={style.tituloNotFound}>404</h1>
   <p id={style.pNotFound}>¡Ups! La página que buscas no existe.</p>
  </div>)
}

export default Not_Found;