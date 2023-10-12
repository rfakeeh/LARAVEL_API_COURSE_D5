
import React, { useEffect, useState } from 'react';
import axios from 'axios';
import ArticleCard from './ArticleCard';
import { Row, Col } from 'react-bootstrap';

export default function Home({ category }) {

    const [subtitle, setSubtitle] = useState('All News'); //default
    const [articles, setArticles] = useState([]);

    useEffect(() => { // load/mount event
 
        let url = 'http://127.0.0.1:8000/api/news';

        if (category != null) {
            url += '?category[]='+category.id;
            setSubtitle(category.name+' News');
        }

        const config = {
            method: 'get',
            url: url,
          };

        console.log(config);

        axios(config)
        .then((result) => {
            console.log(result.data.data.data);
            setArticles(result.data.data.data);
        })
        .catch((error) => {
            console.log(error.response);
        });

      }, [category]);

    return (
        <>
            <Row>
                <Col>
                    <h2 className='text-center'>Home</h2>
                </Col>
            </Row>
            <Row>
                <Col>
                    <h3 className='text-center'>{subtitle}</h3>
                    <br/>
                </Col>
            </Row>
            <Row xs={1} md={2} lg={3}>
                {articles.map((item, idx) => (
                    <ArticleCard key={idx} article={item} />
                ))}
            </Row>

        </>
    );
}