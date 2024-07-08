pipeline {
    agent any

    environment {
        IMAGE_NAME = "back-end-web-unity"
        CONTAINER_NAME = "${env.BUILD_NUMBER}-${new Date().format('yyyyMMdd-HHmmss')}-${IMAGE_NAME}"
    }

    stages {
        stage('Checkout') {
            steps {
                // Checkout the code from the GitHub repository
                git url: 'git@github.com:UNSRAT-IT-Community/back-end-web-unity.git', branch: 'main'
            }
        }
        stage('Build Docker Image') {
            steps {
                script {
                    // Build the Docker image
                    sh "docker build -t ${IMAGE_NAME}:latest ."
                }
            }
        }
        stage('Update docker-compose.yml') {
            steps {
                script {
                    // Update the docker-compose.yml file with the dynamic container name
                    sh """
                    sed -i 's/CONTAINER_NAME/${CONTAINER_NAME}/g' docker-compose.yml
                    """
                }
            }
        }
        stage('Docker Compose') {
            steps {
                script {
                    // Deploy using Docker Compose
                    sh 'docker-compose up --build -d'
                }
            }
        }
    }

    post {
        always {
            // Cleanup docker containers and images
            sh 'docker-compose down'
        }
    }
}
