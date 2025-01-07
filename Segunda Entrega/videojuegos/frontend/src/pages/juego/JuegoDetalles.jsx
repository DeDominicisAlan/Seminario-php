import React, { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import imgNotFound from "../../assets/images/not-found.jpg";
import styles from "../../assets/styles/JuegoDetalle.module.css";
import { useAuth } from "../../config/auth";
import Cookies from "cookies-js";
import { realizarSolicitud, BASE_URL } from "../../config/data"

const Juego = () => {
  const { isLoggedIn } = useAuth();
  const { id } = useParams();
  const [juego, setJuego] = useState(null);
  const [calificaciones, setCalificaciones] = useState(null);
  const [soporte, setSoporte] = useState(null);

  useEffect(() => {
    var obtenerJuegos = async () => {
      const respuestaJson = await realizarSolicitud(`${BASE_URL}juegos/${id}`);
      if (respuestaJson.Codigo === 200) {
        //console.log(respuestaJson);
        //console.log(respuestaJson.Data.Juego);
        setCalificaciones(respuestaJson.Data.Calificaciones);
        setJuego(respuestaJson.Data.Juego);
        setSoporte(respuestaJson.Data.Soporte);
        //console.log(respuestaJson.Data.Soporte);
        //console.log(soporte);
      } else {
        setTimeout(() => {
          window.location.assign("/*");
        }, 0);
      }
    };
    obtenerJuegos();
  }, []);

  const isValidBase64 = (str) => {
    const base64Pattern =
      /^data:image\/(jpeg|png|gif);base64,[A-Za-z0-9+/]+={0,2}$/;
    return base64Pattern.test(str);
  };

  const detectarId = (usuario_id) => {
    if (isLoggedIn) {
      const id = Cookies.get("id");
      if (usuario_id === parseInt(id)) return styles.calificacionDelUsuario;
    }
    return;
  };

  const imagenValida = (imagen) => {
    if (isValidBase64(imagen)) {
      return imagen;
    }
    return imgNotFound;
  };

  if (!juego)
    return (
      <div className={styles.contenedorPagina}>
        <h2>Cargando...</h2>
      </div>
    );

  return (
    <div className={styles.contenedorPagina}>
      <div id={styles.contenedor}>
        <h1>{juego.nombre}</h1>
        <div id={styles.juegoContenedor}>
          <img
            src={imagenValida("data:image/jpeg;base64," + juego.imagen)}
            alt={juego.nombre}
          />
          <div id={styles.descripcion}>
            <p>{juego.descripcion}</p>
            <p>Clasificación por edad: {juego.clasificacion_edad}</p>
            <p>
              Plataformas: {soporte && soporte.map((s) => s.nombre).join(", ")}
            </p>
            <div id={styles.calificaciones}>
              {calificaciones ? (
                <ul id={styles.lista}>
                  {calificaciones
                    .sort((a, b) =>{ //Priorizamos ordenando al usuario logueado, mostrandolo primero
                      const id = isLoggedIn ? parseInt(Cookies.get("id")) : null;
                      let esUsuarioA = a.usuario_id === id;
                      let esUsuarioB = b.usuario_id === id;
                      
                      if (esUsuarioA && !esUsuarioB) return -1; 
                      if (!esUsuarioA && esUsuarioB) return 1;
                      return b.estrellas - a.estrellas; 
                    } )
                    .map((c) => {
                      return (
                        <li key={c.id} className={detectarId(c.usuario_id)}>
                          {c.estrellas} estrellas {c.nombre_usuario}
                        </li>
                      );
                    })}
                </ul>
              ) : (
                <p>Este juego no tiene calificaciones hechas</p>
              )}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Juego;
