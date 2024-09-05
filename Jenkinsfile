pipeline {
    agent {
        docker {
            image 'symfony_base-php'
            // Utiliser des chemins Linux pour Docker sur Windows
            args '-v //c/ProgramData/Jenkins:/workspace -w /workspace'
        }
    }

    stages {
        stage('Checkout') {
            steps {
                git branch: 'main', url: 'https://github.com/Kiliroystrik/symfony_tests-cours.git'
            }
        }

        stage('Install') {
            steps {
                script {
                    sh 'composer install --no-interaction --prefer-dist'
                }
            }
        }

        stage('Tests') {
            steps {
                script {
                    sh './vendor/bin/phpunit'
                }
            }
        }
    }
}
