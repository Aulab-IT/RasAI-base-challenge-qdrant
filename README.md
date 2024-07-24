## TODO>>

### Implement the Vector Database and Embeddings
- [ ] Create the embeddings when a document is created
    - [ ] Add a library to read PDF files
    - [ ] Install "smalot/pdfparser" with `composer require smalot/pdfparser`
    - [ ] Create a function in Document Model to extract the text from a file
    - [ ] Create in DocumentSplitterService a static function to split the text into paragraphs
    - [ ] Create a static function in OpenAiService to get the embeddings from OpenAI
    - [ ] For each paragraph, create an embedding
- [ ] Create QdrantService to interact with the [Qdrant API](https://qdrant.tech/documentation/quickstart-cloud/)
    - [ ] Install ```qdrant-php``` with composer [Qdrant PHP](https://github.com/hkulekci/qdrant-php)
    - [ ] Create a cluster in qdrant cloud and set the API key in the .env file [Qdrant Cloud](https://cloud.qdrant.io/)
    - [ ] Create a [Custom command](https://laravel.com/docs/11.x/artisan#generating-commands) to create a collection in Qdrant Database
    - [ ] Create a function to add the embeddings to the Qdrant database


### Implement the RAG
- [ ] Implement the generateOpenAiResponse method in ```Chatbot.php```
- [ ] Implement the generateSystemPrompt method in ```Chatbot.php```
    - [ ] Create a vector from the message send by the user
    - [ ] Retrive the most similar embeddings from the database
    - [ ] Return the context of the most similar embeddings
    - [ ] Return the system prompt with the context
- [ ] Save the message in the database
    
### EXTRA 
- [ ] Implement createChatTitle in ```OpenAiService.php``` to create a title for the chat