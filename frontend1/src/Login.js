import React from 'react';
import { Row, Col, Form, Button } from 'react-bootstrap';
import { useState } from 'react';
import axios from 'axios';
import Cookies from 'universal-cookie';
import { useNavigate } from 'react-router-dom';

const cookies = new Cookies();

export default function Login() {

    const [validated, setValidated] = useState(false);

    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');

    const [loggedIn, setLoggedIn] = useState(false);
    const [message, setMessage] = useState('');

    const navigate = useNavigate();

    const handleSubmit = (event) => {

        event.preventDefault();
        event.stopPropagation();

        if (validated === false) {
            setValidated(true);
        } // bootstrap

        const form = event.currentTarget;
        if (form.checkValidity() === true) {
            const config = {
                method: 'POST',
                url: 'http://127.0.0.1:8000/api/user/login',
                data: {
                    email,
                    password,
                },
            };
            
            axios(config)
            .then((result) => {
                console.log(result.data);
                setLoggedIn(true);
                setMessage(result.data.message);
                
                const seconds = 24*60*60;
                const date = new Date();
                date.setTime(date.getTime() + seconds * 1000);
                
                cookies.set('token', result.data.token, {
                    path: '/',
                    expires: date,
                });

                //window.location.href = '/dash';
                navigate('/dash');
            })
            .catch((error) => {
                console.log(error.response.data);
                setLoggedIn(false);
                setMessage(error.response.data.message);
            })   
        }  
    }

    return (
        <>
            <Row className='login-form'>
                <Col xs={10} sm={10} md={4} lg={4} xl={4}>
                    <h1>Login</h1>
                    <Form noValidate validated={validated} onSubmit={handleSubmit}>
                        {/* email */}
                        <Form.Group controlId='frmEmail'>
                            <Form.Label>Email:</Form.Label>
                            <Form.Control 
                                type='email' 
                                name='email'
                                value={email}
                                placeholder='Enter email' 
                                onChange={(event) => setEmail(event.target.value)}
                                required />
                            <Form.Control.Feedback type="invalid">Please enter a valid email.</Form.Control.Feedback>
                        </Form.Group>
                        {/* password */}
                        <Form.Group controlId='frmPassword'>
                            <Form.Label>Password:</Form.Label>
                            <Form.Control 
                                type='password'  
                                name='password'
                                value={password}
                                placeholder='Enter password' 
                                onChange={(event) => setPassword(event.target.value)}
                                required 
                                minLength={8} />
                            <Form.Control.Feedback type='invalid'>Please enter a valid password.</Form.Control.Feedback>
                        </Form.Group>
                        {/* submit button */}
                        <Button type='submit' variant='primary'>Submit</Button>
                    </Form>
                    <br/>
                    {/* display server message */}
                    {loggedIn ? (
                        <p className='text-success'>{message}</p>
                    ) : (
                        <p className='text-danger'>{message}</p>
                    )}
                </Col>
            </Row>
        </>
    )
}