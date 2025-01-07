import React, { useEffect, useState } from "react";
import style from '../assets/styles/Load.module.css';

function PreLoad() {
  const [loading, setLoading] = useState(true);
  const [completed, setCompleted] = useState(false);

  useEffect(() => {
    
    setTimeout(() => {
      setLoading(false); 
      
      setTimeout(() => {
        setCompleted(true); 
      }, 1000);
    }, 2000);
  }, []);

  return (
    <>
      {!completed ? (
        <div className={`${style.loadingOverlay} ${!loading ? style.hidden : ""}`}>
          <div className={style.spinner}>
            <div className={style.halfSpinner}></div>
          </div>
        </div>
      ) : (
        <></>
      )}
    </>
  );
}

export default PreLoad;
