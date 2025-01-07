import React, { useEffect, useState } from "react";
import styles from "../../assets/styles/Calificacion.module.css";
import Cookies from "cookies-js";
import { realizarSolicitud, BASE_URL } from "../../config/data";
import { imagenValida } from "../../config/validarImagen";

const Calificar = () => {
  const [tieneCalificacion, setTieneCalificacion] = useState(null);
  const [calificacion, setCalificacion] = useState(3);
  const [calificacionId, setCalificacionId] = useState(-1);
  const [respuesta, setRespuesta] = useState("");
  const [juego, setJuego] = useState(null);
  const token = Cookies.get("token");
  const idJuego = localStorage.getItem("id_juego");

  useEffect(() => {
    const idCalificacion = localStorage.getItem("calificacionId");
    const hayCalificacion = localStorage.getItem("calificacion");
    setCalificacionId(parseInt(idCalificacion));
    setTieneCalificacion(hayCalificacion === "true");

    const obtenerJuego = async () => {
      const respuestaJson = await realizarSolicitud(
        `${BASE_URL}juegos/${idJuego}`
      );
      if (respuestaJson.Codigo === 200) {
        setTimeout(setJuego(respuestaJson.Data.Juego), 0.1);
      }
    };
    obtenerJuego();
  }, []);

  const handleCalificacionChange = (e) => {
    setCalificacion(parseInt(e.target.value));
  };

  const editarCalificacion = async () => {
    const data = {
      estrellas: calificacion,
    };
    const respuestaJson = await realizarSolicitud(
      `${BASE_URL}/calificacion/${calificacionId}`,
      "PUT",
      data,
      token
    );
    setRespuesta(respuestaJson.Mensaje);
    setTimeout(() => {
      window.location.assign("/juegos");
    }, 2000);
  };

  const crearCalificacion = async () => {
    const data = {
      estrellas: calificacion,
      juego_id: parseInt(idJuego),
    };
    const respuestaJson = await realizarSolicitud(
      `${BASE_URL}calificacion`,
      "POST",
      data,
      token
    );
    setRespuesta(respuestaJson.Mensaje);
    setTimeout(() => {
      window.location.assign("/juegos");
    }, 2000);
  };

  const borrarCalificacion = async () => {
    const data = {};
    const respuestaJson = await realizarSolicitud(
      `${BASE_URL}calificacion/${calificacionId}`,
      "DELETE",
      data,
      token
    );
    setRespuesta(respuestaJson.Mensaje);
    setTimeout(() => {
      window.location.assign("/juegos");
    }, 2000);
  };

  return (
    <div id={styles.contenedor}>
      <div id={styles.contenedorCarta}>
        <div>
          {juego ? (
            <div>
              <p id={styles.nombre}>{juego.nombre}</p>
              <img
                src={imagenValida("data:image/jpeg;base64," + juego.imagen)}
                alt="Imagen"
                id={styles.imagen}
              />
            </div>
          ) : (
            <div></div>
          )}
        </div>
        <form id={styles.form} onSubmit={(e) => e.preventDefault()}>
          <h1 id={styles.tituloCalificacion}>Calificación</h1>
          <h3 id={styles.respuesta}>{respuesta}</h3>
          <div className={styles.rating}>
            {[5, 4, 3, 2, 1].map((valor) => (
              <React.Fragment key={valor}>
                <input
                  value={valor}
                  name="rate"
                  id={`star${valor}`}
                  type="radio"
                  checked={calificacion === valor}
                  onChange={handleCalificacionChange}
                />
                <label
                  title={`${valor} estrellas`}
                  htmlFor={`star${valor}`}></label>
              </React.Fragment>
            ))}
          </div>
          {tieneCalificacion ? (
            <div>
              <button type="submit" onClick={editarCalificacion}>
                Editar Calificación
              </button>
              <button type="button" onClick={borrarCalificacion}>
                Borrar
              </button>
            </div>
          ) : (
            <button onClick={crearCalificacion} type="submit">
              Crear Calificación
            </button>
          )}
        </form>
      </div>
    </div>
  );
};

export default Calificar;
