import React, { useEffect, useState } from 'react';
import { Row, Col, Image, Nav, Form, Button } from 'react-bootstrap';
import axios from 'axios';
import Cookies from 'universal-cookie';
import { useNavigate } from 'react-router-dom';

const cookies = new Cookies();

export default function AddArticle({ categories }) {

    const token = cookies.get('token');

    const [validated, setValidated] = useState(false);

    const [title, setTitle] = useState('');
    const [body, setBody] = useState('');
    const [thumbnail, setThumbnail] = useState('');
    const [visible, setVisible] = useState(false);
    const [completed, setCompleted] = useState(false);
    const [selectedCategories, setSelectedCategories] = useState([]);
    const [images, setImages] = useState([]);

    const [created, setCreated] = useState(false);
    const [message, setMessage] = useState('');   

    const [user, setUser] = useState({
        name: 'Anonymous',
        avatar: 'https://placehold.co/400x400',
        total: 0,
        public: 0,
        private: 0,
        finished: 0,      
    });
    
    useEffect(() => {
        const config = {
            method: 'get',
            url: 'http://127.0.0.1:8000/api/user/',
            headers: {
              Authorization: `Bearer ${token}`,
            },
          };

        axios(config)
          .then((result) => {
            let user = result.data.data;
            console.log(user);
            setUser({
                name: user.name,
                avatar: 'http://127.0.0.1:8000/storage/'+user.avatar,
                total: user.total_news_count,
                public: user.public_news_count,
                private: user.private_news_count,
                finished: user.completed_news_count,      
            });
          })
          .catch((error) => {
            console.log(error.response);
          });

      }, []);

        const navigate = useNavigate();

        const handleCategoriesChange = (event) => {
            const value = event.target.value;
            if (event.target.checked) {
                setSelectedCategories([...selectedCategories, value]);
            } else {
                setSelectedCategories(selectedCategories.filter((option) => option !== value));
            }
        };

        const handleSubmit = (event) => {

            event.preventDefault();
            event.stopPropagation();
            if (validated === false) {
                setValidated(true);
            }
            const form = event.currentTarget;
            if (form.checkValidity() === true) {

                let formData = new FormData();
                formData.append('title',title);
                formData.append('body',body);
                formData.append('thumbnail',thumbnail);
                formData.append('visible',visible ? 1 : 0);
                formData.append('completed',completed ? 1 : 0);
                selectedCategories.forEach((option) => {
                    formData.append('categories[]',option);
                });

                const config = {
                    method: 'POST',
                    url: 'http://127.0.0.1:8000/api/user/news',
                    headers: {
                      'Authorization': `Bearer ${token}`,
                    },
                    data: formData,
                };
                axios(config)
                .then((result) => {
                    console.log(result.data);
                    setCreated(true);
                    setMessage(result.data.message);
                    //window.location.href = '/dash';
                    navigate('/dash');
                })
                .catch((error) => {
                    console.log(error.response.data);
                    setCreated(false);
                    setMessage(error.response.data.message);
                })   
            }  
        }

        const logout = () => {
            // destroy the cookie
            cookies.remove('token', { path: '/' });
            navigate('/');
          }

    return (
        <>
          <Row>
            <Col lg={2} className='side-bar'>
                    <Row>
                        <Col><Image src={user.avatar} roundedCircle fluid /></Col>
                    </Row>
                    <Row>
                        <Col><p>{user.name}</p></Col>
                    </Row>
                    <Row>
                        <Col>
                            <Nav className='flex-column'>
                                <Nav.Link href='/dash'>Dashboard</Nav.Link>
                                <Nav.Link href='/add'>Add Article</Nav.Link>
                                <Nav.Link onClick={() => logout()}>Logout</Nav.Link>
                            </Nav>
                        </Col>
                    </Row>
                </Col>
                <Col>
                    <Row>
                        <Col>
                            <h2>AddArticle</h2>
                        </Col>
                    </Row>
                    <Row>
                        <Col xs={10} sm={10} md={4} lg={6} xl={6}>
                            <Form noValidate validated={validated} onSubmit={handleSubmit}>
                                {/* title */}
                                <Form.Group controlId='frmTitle'>
                                    <Form.Label>Title:</Form.Label>
                                    <Form.Control 
                                        type='text' 
                                        name='title'
                                        value={title}
                                        placeholder='Enter article title' 
                                        onChange={(event) => setTitle(event.target.value)}
                                        required />
                                    <Form.Control.Feedback type="invalid">Please enter a valid article title.</Form.Control.Feedback>
                                </Form.Group>
                                {/* thumbnail */}
                                <Form.Group controlId='frmThumbnail'>
                                    <Form.Label>Thumbnail</Form.Label>
                                    <Form.Control 
                                        type='file'
                                        name='thumbnail'
                                        onChange={(event) => setThumbnail(event.target.files[0])}
                                        required
                                    />
                                </Form.Group>
                                {/* body */}
                                <Form.Group controlId='frmBody'>
                                    <Form.Label>Body</Form.Label>
                                    <Form.Control 
                                        as='textarea' 
                                        rows={5} 
                                        name='body'
                                        value={body}
                                        placeholder='Enter article body' 
                                        onChange={(event) => setBody(event.target.value)}
                                    />
                                </Form.Group>
                                {/* visible */}
                                <Form.Check 
                                    type='switch'
                                    id='frmVisible'
                                    label='Visible'
                                    name='visible'
                                    value={visible}
                                    onChange={(event) => setVisible(event.target.value)}
                                />
                                {/* completed */}
                                <Form.Check 
                                    type='switch'
                                    id='frmCompleted'
                                    label='Completed'
                                    name='completed'
                                    value={completed}
                                    onChange={(event) => setCompleted(event.target.value)}
                                />
                                <hr/>
                                {/* categories */}
                                <Row xs={2} md={4} lg={4}>
                                    {categories.map((item, idx) => (
                                        <Col key={idx}>
                                            <Form.Check 
                                                type='checkbox'
                                                id={idx}
                                                label={item.name}
                                                name='categories'
                                                value={item.id}
                                                onChange={handleCategoriesChange} 
                                            />
                                        </Col>                                        
                                    ))}        
                                </Row>
                                <hr/>
                                {/* submit button */}
                                <Button type='submit' variant='primary'>Submit</Button>
                            </Form>
                            <br/>
                            {/* display server message */}
                            {created ? (
                                <p className='text-success'>{message}</p>
                            ) : (
                                <p className='text-danger'>{message}</p>
                            )}
                        </Col>
                    </Row>
                </Col>
            </Row>
        </>
    );
}