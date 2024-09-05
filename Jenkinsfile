pipeline {
    agent {
        docker {
            image 'symfony_base-php'
            args '-p 8080:80'
        }
    }

    stages {
        stage('Checkout') {
            steps {
                workingDir '/app' // specify the working directory for this stage
                git branch: 'main', url: 'https://github.com/Kiliroystrik/symfony_tests-cours.git'
            }
        }

        stage('Install') {
            steps {
                workingDir '/app' // specify the working directory for this stage
                script {
                    // Installer les dépendances Composer
                    sh 'composer install --no-interaction --prefer-dist'
                }
            }
        }

        stage('Tests') {
            steps {
                workingDir '/app' // specify the working directory for this stage
                script {
                    // Lancer PHPUnit
                    sh './vendor/bin/phpunit'
                }
            }
        }
    }
}