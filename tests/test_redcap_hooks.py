# -*- coding: utf-8 -*-

# To run:
#   python tests/test_redcap_hooks.py

from selenium import webdriver
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.support.ui import Select
from selenium.common.exceptions import NoSuchElementException
from selenium.common.exceptions import NoAlertPresentException
import unittest, time, re
import os


class TestRedcapHooks(unittest.TestCase):
    def setUp(self):
        self.driver = webdriver.PhantomJS()
        self.driver.set_window_size(1024, 800)
        self.driver = webdriver.Firefox()
        self.driver.implicitly_wait(3)
        self.base_url = "http://localhost"
        self.base_url = "http://localhost:8080"
        self.verificationErrors = []
        self.accept_next_alert = True

    def test_redcap_hooks(self):
        driver = self.driver
        try:
            driver.get(self.base_url + "/redcap/index.php")
            driver.find_element_by_css_selector("font").click()
            driver.find_element_by_id("app_title").clear()
            driver.find_element_by_id("app_title").send_keys("This is the Project Title")
            Select(driver.find_element_by_id("purpose")).select_by_visible_text("Practice / Just for fun")
            driver.find_element_by_css_selector("input[type=\"button\"]").click()
            driver.find_element_by_xpath("//div[@id='setupChklist-design']/table/tbody/tr/td[2]/div[2]/div/button").click()
            driver.find_element_by_id("formlabel-my_first_instrument").click()
            driver.find_element_by_id("btn-last").click()
            Select(driver.find_element_by_id("field_type")).select_by_visible_text("Text Box (Short Text)")
            driver.find_element_by_id("field_label").clear()
            driver.find_element_by_id("field_label").send_keys("How many times did the event occur?")
            driver.find_element_by_id("field_name").click()
            driver.find_element_by_id("field_name").clear()
            driver.find_element_by_id("field_name").send_keys("occurrences")
            Select(driver.find_element_by_id("val_type")).select_by_visible_text("Integer")
            driver.find_element_by_id("val_min").clear()
            driver.find_element_by_id("val_min").send_keys("0")
            driver.find_element_by_id("val_max").clear()
            driver.find_element_by_id("val_max").send_keys("50")
            driver.find_element_by_id("field_note").clear()
            driver.find_element_by_id("field_note").send_keys("0-50 (<span class=valid>-1</span> if unknown)")
            driver.find_element_by_xpath("(//button[@type='button'])[2]").click()
            time.sleep(0.3)
            driver.find_element_by_link_text("Add / Edit Records").click()
            time.sleep(0.3)
            driver.find_element_by_link_text("Add / Edit Records").click()
            time.sleep(0.3)
            driver.find_element_by_id("inputString").clear()
            driver.find_element_by_id("inputString").send_keys("007")
            driver.find_element_by_id("inputString").send_keys(Keys.ENTER)
            driver.find_element_by_name("occurrences").clear()
            driver.find_element_by_name("occurrences").send_keys("-1")
            Select(driver.find_element_by_name("my_first_instrument_complete")).select_by_visible_text("Complete")
            driver.find_element_by_name("submit-btn-cancel").click()
            driver.find_element_by_link_text("Project Home").click()
            driver.find_element_by_link_text("My Projects").click()
            driver.find_element_by_link_text("Control Center").click()
            driver.find_element_by_link_text("General Configuration").click()
            os.symlink('examples/redcap_data_entry_form', 'hooks/redcap_data_entry_form')
            driver.find_element_by_name("hook_functions_file").clear()
            driver.find_element_by_name("hook_functions_file").send_keys("/redcap_data/hooks/redcap_hooks.php")
            driver.find_element_by_css_selector("input[type=\"submit\"]").click()
            driver.find_element_by_link_text("My Projects").click()
            driver.find_element_by_link_text("This is the Project Title").click()
            driver.find_element_by_link_text("Add / Edit Records").click()
            driver.find_element_by_id("inputString").clear()
            driver.find_element_by_id("inputString").send_keys("007_2")
            driver.find_element_by_id("inputString").send_keys(Keys.ENTER)
            driver.find_element_by_name("occurrences").clear()
            driver.find_element_by_name("occurrences").send_keys("-1")
            Select(driver.find_element_by_name("my_first_instrument_complete")).select_by_visible_text("Complete")
            driver.find_element_by_css_selector("option[value=\"2\"]").click()
            driver.find_element_by_name("submit-btn-saverecord").click()
            Select(driver.find_element_by_id("record_select2")).select_by_visible_text("007_2")
            try: self.assertEqual("-1", driver.find_element_by_name("occurrences").get_attribute("value"))
            except AssertionError as e: self.verificationErrors.append(str(e))
            driver.find_element_by_name("submit-btn-delete").click()
            driver.find_element_by_xpath("(//button[@type='button'])[2]").click()
            driver.find_element_by_link_text("My Projects").click()
            driver.find_element_by_link_text("This is the Project Title").click()
            driver.find_element_by_css_selector("li > a").click()
            driver.find_element_by_link_text("Other Functionality").click()
            driver.find_element_by_xpath("//input[@value='Delete the project']").click()
            driver.find_element_by_id("delete_project_confirm").clear()
            driver.find_element_by_id("delete_project_confirm").send_keys("DELETE")
            driver.find_element_by_xpath("(//button[@type='button'])[2]").click()
            driver.find_element_by_xpath("(//button[@type='button'])[4]").click()
            driver.find_element_by_xpath("(//button[@type='button'])[3]").click()
            driver.find_element_by_link_text("My Projects").click()
            os.remove('hooks/redcap_data_entry_form')
        except:
            driver.get_screenshot_as_file('screenshot-test_redcap_hooks.png')
            raise

    def is_element_present(self, how, what):
        try: self.driver.find_element(by=how, value=what)
        except NoSuchElementException, e: return False
        return True

    def is_alert_present(self):
        try: self.driver.switch_to_alert()
        except NoAlertPresentException, e: return False
        return True

    def close_alert_and_get_its_text(self):
        try:
            alert = self.driver.switch_to_alert()
            alert_text = alert.text
            if self.accept_next_alert:
                alert.accept()
            else:
                alert.dismiss()
            return alert_text
        finally: self.accept_next_alert = True

    def tearDown(self):
        self.driver.quit()
        self.assertEqual([], self.verificationErrors)

if __name__ == "__main__":
    unittest.main()
