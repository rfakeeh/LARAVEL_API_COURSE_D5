import React from 'react';
import { Card, Col } from 'react-bootstrap';

export default function ArticleCard({ article }) {

    return (
        <Col key={article.id}>
        <Card>
          <Card.Img variant='top' src={ article.thumbnail.startsWith('thumbnails/') ? 'http://127.0.0.1:8000/storage/'+article.thumbnail : 'https://placehold.co/600x400'} />
          <Card.Body>
            <Card.Title>{article.title}</Card.Title>
            <Card.Text>
                By: {article.author_name}
            </Card.Text>
            <Card.Text>
                {article.categories.map((item, idx) => (
                    <span className='tag' key={idx}>#{item.name.replaceAll(' ','_')}, </span>
                ))}
            </Card.Text>
          </Card.Body>
          <Card.Footer>
            <small className='text-muted'>Last updated: {article.updated_at.split('T')[0]}</small>
          </Card.Footer>
        </Card>
        <br/>
      </Col>
    )
}