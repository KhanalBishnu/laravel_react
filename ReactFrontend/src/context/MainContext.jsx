import { createContext, useContext, useState } from "react";

const StateContext = createContext({
  user: null,
  token: null,
  setUser:()=>{},
  setToken:()=>{}
});

export const ContextProvider = ({ childern }) => {
  const [user, setUser] = useState({});
  const [token, _setToken] = useState(1234);
  const setToken = (token) => {
    _setToken(token);
    if (token) {
      localStorage.setItem("ACCESS_TOKEN", token);
    } else {
      localStorage.removeItem("ACCESS_TOKEN");
    }
  };
  return (
    <StateContext.Provider
      value={{
        user,
        token,
        setToken,
        setUser
      }}
    >
      {childern}
    </StateContext.Provider>
  );
};

export const  useStateContext=()=>useContext(StateContext)
