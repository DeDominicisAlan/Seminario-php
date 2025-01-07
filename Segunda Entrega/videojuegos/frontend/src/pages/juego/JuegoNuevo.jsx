import Cookies from "cookies-js";
import React, { useEffect, useRef, useState } from "react";
import { useAuth } from "../../config/auth";
import style from "../../assets/styles/JuegoNuevo.module.css";
import {realizarSolicitud, BASE_URL} from "../../config/data"

const JuegoNuevo = () => {
  const { isLoggedIn } = useAuth();
  const refNombre = useRef(null);
  const refDescripcion = useRef(null);
  const refImagen = useRef(null);
  const refClasificacion = useRef(null);
  const [respuesta, setRespuesta] = useState("");
  const [plataformas, setPlataformas] = useState([]);
  const [plataformasSeleccionadas, setPlataformasSeleccionadas] = useState([]);

  useEffect(() => {
    
    const timeoutId = setTimeout(() => {
      const isAdmin = parseInt(Cookies.get("a"));
      if (isAdmin === 0 || !isLoggedIn) {
        window.location.assign("/juegos");
      }
    }, 2000);

    return () => clearTimeout(timeoutId);
  }, [isLoggedIn]);

  const handleEnviarSolicitud = async () => {
    const token = Cookies.get("token");

    const formData = new FormData();
    formData.append("nombre", refNombre.current.value);
    formData.append("descripcion", refDescripcion.current.value);
    formData.append("imagen", refImagen.current.files[0]);
    formData.append("clasificacion_edad", refClasificacion.current.value);

    plataformasSeleccionadas.forEach((id, index) => {
      formData.append(`plataformas[${index}]`, id);
    });

    const respuestaJson = await realizarSolicitud(
     `${BASE_URL}juego`,
      "POST",
      formData,
      token,
      false
    );

    if (respuestaJson.Codigo === 200) {
      const { juegoId } = respuestaJson.Data;
      const plataformaPromises = plataformasSeleccionadas.map(async (id) => {
        const plataformaData = new FormData();
        plataformaData.append("juego_id", juegoId);
        plataformaData.append("plataforma_id", id);

        return await realizarSolicitud(
          `${BASE_URL}soporte`,
          "POST",
          plataformaData,
          token,
          false
        );
      });

      // Esperamos a que todas las solicitudes de agregar plataforma se completen
      await Promise.all(plataformaPromises);

      setTimeout(() => {
        window.location.assign("/juegos");
      }, 2000);
    }
    setRespuesta(respuestaJson.Mensaje);
  };

  const handleCheckboxChange = (id) => {
    setPlataformasSeleccionadas((prevSeleccionadas) => {
      if (prevSeleccionadas.includes(id)) {
        // Si ya está seleccionada, la sacamos
        return prevSeleccionadas.filter((plataformaId) => plataformaId !== id);
      } else {
        // Si no está seleccionada, la agregamos
        return [...prevSeleccionadas, id];
      }
    });
  };

  useEffect(() => {
    var obtenerPlataformas = async () => {
      const respuestaJson = await realizarSolicitud(`${BASE_URL}plataformas`);
      console.log(respuestaJson);
      if (respuestaJson.Codigo === 200) {
        setPlataformas(respuestaJson.Data.plataformas);
        console.log(plataformas);
      }
    };
    obtenerPlataformas();
  }, []);

  return (
    <div id={style.contenedor}>
      <h2 >{respuesta && <h2 className={style.h2Respuesta}>{respuesta}</h2>}</h2>

      <div id={style.formularioContainer}>
        <form
          id={style.formulario}
          onSubmit={(e) => {
            e.preventDefault();
            handleEnviarSolicitud();
          }}>
          <div>
            <p className={style.campo}>Nombre del Juego</p>
            <input
              type="text"
              id="nombre"
              name="nombre"
              ref={refNombre}
              maxLength={45}
              required
              className={style.input}
            />
          </div>

          <div>
            <p className={style.campo}>Descripcion del Juego</p>
            <textarea
              id={style.textarea}
              name="descripcion"
              cols="30"
              rows="5"
              maxLength={300}
              required
              ref={refDescripcion}></textarea>
          </div>

          <div>
            <p className={style.campo}>Imagen</p>
            <input
              type="file"
              name="imagen"
              id="imagen"
              required
              className={style.input}
              ref={refImagen}
            />
          </div>

          <div>
            <p className={style.campo}>Clasificacion de Edad</p>
            <select
              name="clasificacion_edad"
              ref={refClasificacion}
              id={style.select}>
              <option value="ATP">ATP</option>
              <option value="+18">+18</option>
              <option value="+13">+13</option>
            </select>
          </div>

          <div>
            <p className={style.campo}>Plataformas disponibles</p>
            {plataformas.map((plataforma) => (
              <div key={plataforma.id} className={style.checkboxContainer}>
                <input
                  className={style.input}
                  type="checkbox"
                  id={`plataforma-${plataforma.id}`}
                  value={plataforma.id}
                  onChange={() => handleCheckboxChange(plataforma.id)}
                />
                <label htmlFor={`plataforma-${plataforma.id}`}>
                  {plataforma.nombre}
                </label>
              </div>
            ))}
          </div>

          <input type="submit" value="Enviar" id={style.submitButton} />
        </form>

      </div>
    </div>
  );
};

export default JuegoNuevo;
