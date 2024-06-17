## TODO>>

### Implement the Vector Database and Embeddings
- [ ] Create migrations for the database
    - [ ] Create a migration `add_vector_extension_to_postgresql` to add the vector extension to the database
    - [ ] Create a migration `create_embeddings_table` to create the table `embeddings` in the database
        - Add the columns `content`, `embedding`, `document_id` to the table 
- [ ] Create a model for the embeddings
    - [ ] Create a model `Embedding` to represent the embeddings
    - [ ] Add relationship between the `Document` and `Embedding` models
- [ ] Add the embeddings to the database when a document is created
    - [ ] Add a library to read PDF files
    - [ ] Install "smalot/pdfparser" with `composer require smalot/pdfparser`
    - [ ] Create a function in Document Model to extract the text from a file
    - [ ] Create in DocumentSplitterService a static function to split the text into paragraphs
    - [ ] Create a static function in OpenAiService to get the embeddings from OpenAI
    - [ ] For each paragraph, create an embedding and save it in the database

### Implement the RAG
- [ ] Implement the generateOpenAiResponse method in ```Chatbot.php```
- [ ] Implement the generateSystemPrompt method in ```Chatbot.php```
    - [ ] Implement the getContextFromKnowledgeBase method in ```Chatbot.php```
        - [ ] Create a vector from the message send by the user
        - [ ] Retrive the most similar embeddings from the database
        - [ ] Return the context of the most similar embeddings
    - [ ] Return the system prompt with the context
- [ ] Save the message in the database
    
### EXTRA 
- [ ] Implement createChatTitle in ```OpenAiService.php``` to create a title for the chat