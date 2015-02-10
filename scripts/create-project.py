#!/usr/bin/env python

"""
Creates a REDCap Project using Selenium

Requires:
    - selenium
    - phantomjs (optional)

To install them on OS X:
  $> pip install selenium
  $> brew install phantomjs

"""

import argparse
import sys

from selenium import webdriver
from selenium.webdriver.support.ui import Select

DEFAULT_URL = 'http://127.0.0.1:8000/redcap/index.php'
DEFAULT_PROJECT_NAME = 'RedCapExtras'


def create_project(base_url, project_name, headless=False, username=None,
                   password=None):
    if headless:
        driver = webdriver.PhantomJS()
        driver.set_window_size(1024, 768)
    else:
        driver = webdriver.Firefox()

    try:
        driver.implicitly_wait(3)

        driver.get(base_url + "/redcap/index.php")

        if username:
            print "Logging in..."
            driver.find_element_by_id('username').send_keys(username)
            driver.find_element_by_id('password').send_keys(password)

        driver.find_element_by_partial_link_text("Create New Project").click()
        driver.find_element_by_id("app_title").clear()
        driver.find_element_by_id("app_title").send_keys(project_name)
        Select(driver.find_element_by_id("purpose")).select_by_visible_text("Research")
        driver.find_element_by_id("purpose_other[7]").click()
        driver.find_element_by_css_selector("input[type=\"button\"]").click()
        driver.find_element_by_link_text("API").click()
        driver.find_element_by_xpath("//div[@id='apiReqBoxId']/button").click()
        driver.find_element_by_id("api_export").click()
        driver.find_element_by_id("api_import").click()
        driver.find_element_by_id("api_send_email").click()
        driver.find_element_by_xpath("(//button[@type='button'])[2]").click()
        driver.find_element_by_xpath("(//button[@type='button'])[2]").click()
        api_token = driver.find_element_by_id("apiTokenId").text
        print 'API Token: ', api_token
        driver.find_element_by_link_text("Project Home").click()
        print 'Project Homepage: ', driver.current_url
    finally:
        driver.quit()

def prompt(question, default=None):
    default_text = ' '
    if default:
        default_text = ' [{0}] '.format(default)
    return raw_input(question + default_text).strip() or default

def main():
    parser = argparse.ArgumentParser()
    parser.add_argument('-q', '--quiet', action='store_true', default=False,
                        help='run PhantomJS instead of Firefox and assume '
                             'default to all prompts')
    parser.add_argument('-p', '--project', help='name of the REDCap Project',
                        default=DEFAULT_PROJECT_NAME)
    parser.add_argument('-u', '--url', help='base address of the REDCap Server',
                        default=DEFAULT_URL)
    parser.add_argument('-username', '--username', help='Enter username',
                        default=None)
    parser.add_argument('-password', '--password', help='Enter password',
                        default=None)
    args = vars(parser.parse_args())

    url = args['url']
    project_name = args['project']
    username = args['username']
    password = args['password']

    if not args['quiet']:
        url = prompt('REDCap Base Web Address (URL)?', args['url'])
        project_name = prompt('Project name?', args['project'])

    #credentials = args['credentials']

    create_project(url, project_name, args['quiet'], username, password)

if __name__ == '__main__':
    main()
