import React, { useState } from "react";
import AuthUser from "../AuthUser";

function Login() {
  const { http, setToken } = AuthUser();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [passwordValidation, setpasswordValidation] = useState(false);
  const [emailValidation, setemailValidation] = useState(false);
  const [formError, setFormError] = useState('');

  const submitLoginForm = () => {
    if(email==''){
      setemailValidation(true);
    }else{
      setemailValidation(false);
    }
    if(password==''){
      setpasswordValidation(true);
    }else{
      setpasswordValidation(false);
    }
   
    if (email!='' && password!= '') {
      http.post("/login", {email:email,password:password}).then((res) => {
        debugger
        if (res.data.response) {
          setToken(res.data.user, res.data.token);
        } else {
          setFormError(res.data.message || 'An error occurred');
        }
      });
    }
  };

  return (
    <div className="container">
      <div className="row justify-content-center">
        <div className="col-md-5 border border-dark text-dark font-weight-bold rounded mt-3 ">
          <div className="row">
            <label className="border mb-3 text-center px-2 py-2 bg-secondary" htmlFor="">Login Form</label>
            <div className="col-md-12 mb-1">
              <label htmlFor="">Email <sup className="text-danger">*</sup></label>
              <input
                type="email"
                name="email"
                className={`form-control ${emailValidation ? 'border-danger' : ''}`}
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                />
              {emailValidation && <small className="text-danger">Email is required</small>}
            </div>
            <div className="col-md-12">
              <label htmlFor="">Password<sup className="text-danger">*</sup></label>
              <input
                type="password"
                name="password"
                className={`form-control ${passwordValidation? 'border-danger' : ''}`}
                value={password}
                onChange={(e) => setPassword(e.target.value)}
              />
              {passwordValidation && <small className="text-danger">Password is required</small>}
            </div>
            {formError && <div className="col-md-12 my-3"><small className="text-danger">{formError}</small></div>}
            <div className="col-md-12 my-3">
              <button
                type="button"
                className="btn btn-primary"
                onClick={submitLoginForm}
              >
                Submit
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

export default Login;
